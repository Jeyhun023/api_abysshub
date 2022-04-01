<?php

namespace App\Http\Controllers\Api\Store;

use App\Models\Rating;
use App\Models\Product;
use App\Models\ProductIteration;
use App\Events\StoreElasticEvent;
use App\Http\Resources\Store\RatingResource;
use App\Http\Resources\Store\ProductResource;
use App\Http\Resources\Store\ProductCollection;
use App\Http\Requests\Api\Store\Product\RatingRequest;
use App\Http\Requests\Api\Store\Product\FullRatingRequest;
use App\Http\Requests\Api\Store\Product\ProductSubmitRequest;
use App\Http\Requests\Api\Store\Product\ProductUpdateRequest;
use App\Http\Requests\Api\Store\Product\ProductDeleteRequest;
use App\Http\Requests\Api\Store\Product\ProductPlagiarismRequest;

use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

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
            $product->fill($request->validated());
            $product->save();
            if($product->is_submitted){
                event(new StoreElasticEvent($product));
            }
            return $this->successResponse(new ProductResource($product), trans('messages.product_update_success'));
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }

    public function submit(Product $product, ProductSubmitRequest $request)
    {
        if(!$product->name || !$product->tags || !isset(json_decode($product->description)->description) || 
        !isset(json_decode($product->description)->applicability) || !isset(json_decode($product->description)->problemFormulation)){
            return $this->errorResponse(["failed" => [trans('messages.store_fill_details')] ]);
        }
        if($product->is_plagiat){
            return $this->errorResponse(["failed" => [trans('messages.plagiat_error')]]);
        }

        $product->file = $product->draft;
        $product->draft = null;
        $product->is_plagiat = true;
        $product->is_submitted = true;
        $product->save();
        event(new StoreElasticEvent($product));

        return $this->successResponse(new ProductResource($product), trans('messages.product_submitted_success'));
    }

    public function plagiarismCheck(Product $product, ProductPlagiarismRequest $request)
    {
        try {
            $response = Http::get('https://django.abysshub.com/api/plagiarism/check/'.$product->id);
            if($response->failed()){
                return $this->errorResponse(["failed" => [trans('messages.plagiat_error')] ]);
            }
            $product->is_plagiat = false;
            $product->save();
           
            return $this->successResponse(new ProductResource($product), trans('messages.plagiat_success'));
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }

    public function delete(Product $product, ProductDeleteRequest $request)
    {
        try {
            $product->delete();
            return $this->successResponse(new ProductResource($product), trans('messages.product_delete_success'));
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }

    public function show($id)
    {
        $product = Product::with(['user', 'userCave', 'iterations.user'])->findOrFail($id);
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

    public function search()
    {
        $products = Product::orderByDesc('id')->paginate(10);
        return $this->successResponse(new ProductCollection($products), null);
    }
}