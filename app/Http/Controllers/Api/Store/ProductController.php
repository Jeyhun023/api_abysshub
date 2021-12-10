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
            $product->description = $request->description;
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
            switch ($product->status) {
                case 1:
                    Storage::disk('products')->move($product->file, 'live/'.basename($product->file));
                    $product->update(['status' => '2', 'file' => 'live/'.basename($product->file)]);
                    event(new StoreElasticEvent($product));
                    return $this->successResponse(new ProductResource($product), trans('messages.product_submitted_success'));
                  break;
                case 0:
                    return $this->errorResponse(["failed" => [trans('messages.plagiat_error')] ]);
                  break;
                case 2:
                    return $this->errorResponse(["failed" => [trans('messages.product_already_submitted')] ]);
                  break;
                default:
                    return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
            }
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }

    public function plagiarismCheck(Product $product, ProductPlagiarismRequest $request)
    {
        try {
            $file = trim(md5(time()).'.'.$request->extension);
            Storage::disk('products')->put( 'temporary/'.$file, $request->source_code);
            $url = "python3 /var/www/abysshub/public/python/copydetect/check.py ";
            // $url = "python C:/Users/User/Desktop/www/abyss-hub/public/python/copydetect/check.py 2>&1";
            $result = shell_exec( $url . $file .' '. basename($product->file) );        
            $result = 35;

            switch (true) {
                case $result <= 90:
                    if($product->status != 2){
                        Storage::disk('products')->delete($product->file);
                        $product->update(['status' => '1', 'file' => 'temporary/'.$file]);
                    }else{
                        Storage::disk('products')->move('temporary/'.$file, 'live/'.$file);
                        Storage::disk('products')->delete($product->file);
                        $product->update(['file' => 'live/'.$file]);
                    }
                    if($result <= 40){
                        return $this->successResponse($result, trans('messages.plagiat_success'));
                    }else{
                        return $this->successResponse($result, trans('messages.can_be_iteration'));
                    }
                    break;
                case $result <= 100:
                    if($product->status != 2){
                        Storage::disk('products')->delete($product->file);
                        $product->update(['file' => 'temporary/'.$file]);
                    }else{
                        Storage::disk('products')->delete('temporary/'.$file);
                    }
                    return $this->errorResponse(["failed" => [trans('messages.plagiat_error')] ]);
                    break;
                default:
                    return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
                    break;
            }
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

    public function show($id, $slug)
    {
        $product = Product::with(['user', 'iterations'=> function($query) {
                $query->with(['user', 'iterations']);
            }])
            ->where([
                'id' => $id,
                'slug' => $slug
            ])
            ->firstOrFail();

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