<?php

namespace App\Http\Controllers\Api\Store;

use App\Models\Product;
use App\Models\ProductIteration;
use App\Models\Rating;
use App\Http\Requests\Api\Store\Product\ProductPlagiarismRequest;
use App\Http\Requests\Api\Store\Product\ProductSubmitRequest;
use App\Http\Requests\Api\Store\Product\RatingRequest;
use App\Http\Requests\Api\Store\Product\FullRatingRequest;
use App\Http\Requests\Api\Store\Product\ProductUpdateRequest;
use App\Http\Requests\Api\Store\Product\ProductDeleteRequest;
use App\Http\Resources\Store\ProductResource;
use App\Http\Resources\Store\RatingResource;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Events\StoreElasticEvent;
use Illuminate\Support\Facades\Http;

class ProductController extends Controller
{
    use ApiResponser;
    public $user;
    
    public function __construct()
    {
        $this->user = auth('api')->user();
    }

    public function store(Request $request)
    {
        try {
            $product = Product::query()->create([
                'user_id' => $this->user->id,
                'shop_id' => $this->user->shop->id
            ]);
            return $this->successResponse(new ProductResource($product), trans('messages.product_store_success'));
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }

    public function update(Product $product, ProductUpdateRequest $request)
    {
        try {
            $product->name = $request->name;
            $product->tags = collect( explode(',' , $request->tags) );
            $product->slug = Str::slug($request->name);
            $product->description = json_encode($request->details);
            $product->price = $request->price;
            $product->save();
            if($product->status = 2){
                event(new StoreElasticEvent($product));
            }

            return $this->successResponse(new ProductResource($product), trans('messages.product_update_success'));
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }

    public function submit(Product $product, ProductSubmitRequest $request)
    {
        try {
            if($product->name != null & $product->description != null & $product->tags != null ){
                switch ($product->status) {
                    case 0:
                        return $this->errorResponse(["failed" => [trans('messages.not_checked')] ]);
                    break;
                    case 1:
                        return $this->errorResponse(["failed" => [trans('messages.plagiat_error')] ]);
                    break;
                    case 2:
                        $product->status = 3;
                        $product->save();
                        event(new StoreElasticEvent($product));
                        return $this->successResponse(new ProductResource($product), trans('messages.product_submitted_success'));
                    break;
                    case 3:
                        return $this->errorResponse(["failed" => [trans('messages.product_already_submitted')] ]);
                    break;
                    default:
                        return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
                }
            }
            return $this->errorResponse(["failed" => [trans('messages.store_fill_details')] ]);
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }

    public function plagiarismCheck(Product $product, ProductPlagiarismRequest $request)
    {
        try {
            $product->file = $request->source_code;
            $product->save();

            $response = Http::post('http://django.abysshub.com/api/plagiarism/check/'.$product->id.'?json');
            
            if($response->failed()){
                $product->status = 1;
                $product->save();
                return $this->errorResponse(["failed" => [trans('messages.plagiat_error')] ]);
            }
            
            $product->status = 2;
            $product->save();
            
            return $this->successResponse(null, trans('messages.plagiat_success'));
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }

    public function delete(Product $product, ProductDeleteRequest $request)
    {
        try {
            $product->delete();

            return $this->successResponse(null, trans('messages.product_delete_success'));
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }

    public function show($id)
    {
        $product = Product::with(['user', 'userCave', 'iterations'=> function($query) {
                $query->with(['user', 'iterations']);
            }])->findOrFail($id);
        
        activity('product')
            ->event('show')
            ->causedBy($this->user)
            ->performedOn($product)
            ->withProperties(['query' => request()->query('query'), 'ref' => request()->query('ref')])
            ->log( request()->ip() );

        return $this->successResponse(new ProductResource($product));
    }
    
    public function review(Product $product, RatingRequest $request)
    {
        try {
            $rating = Rating::query()->updateOrCreate(
                ['user_id' => $this->user->id, 'product_id' => $product->id],
                ['value' => $request->value]
            );

            return $this->successResponse(new RatingResource($rating), trans('messages.rating_store_success'));
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }

    public function fullReview(Product $product, FullRatingRequest $request)
    {
        try {
            $rating = Rating::query()->updateOrCreate(
                ['user_id' => $this->user->id, 'product_id' => $product->id],
                ['value' => $request->value, 'content' => $request->content]
            );

            return $this->successResponse(new RatingResource($rating), trans('messages.rating_store_success'));
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }
}