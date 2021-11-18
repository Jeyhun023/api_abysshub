<?php

namespace App\Http\Controllers\Api\Store;

use App\Models\Product;
use App\Models\Rating;
use App\Http\Requests\Api\Store\ProductPlagiarismRequest;
use App\Http\Requests\Api\Store\ProductSubmitRequest;
use App\Http\Requests\Api\Store\RatingRequest;
use App\Http\Requests\Api\Store\FullRatingRequest;
use App\Http\Requests\Api\Store\ProductIterateRequest;
use App\Http\Requests\Api\Store\ProductUpdateRequest;
use App\Http\Requests\Api\Store\ProductDeleteRequest;
use App\Http\Resources\Store\ProductResource;
use App\Http\Resources\Store\RatingResource;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;
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

    public function plagiarismCheck(Product $product, ProductPlagiarismRequest $request)
    {
        $file = md5(time()).'.py';
        Storage::disk('products')->put( 'temporary/'.$file, $request->source_code);

        $url = "sudo python3 /var/www/abysshub/public/python/copydetect/check.py 2>&1";
        $result = shell_exec( "php -v" );
        return $result;

        $result = true;

        if($result){
            Storage::disk('products')->move('temporary/'.$file, 'live/'.$file);
            Storage::disk('products')->delete('live/'.$product->file);
            $product->file = $file;
            $product->save();
            return $this->successResponse(null, trans('messages.plagiat_success'));
        }

        Storage::disk('products')->delete('temporary/'.$file);
        return $this->errorResponse(["failed" => [trans('messages.plagiat_error')] ]);
    }

    public function update(Product $product, ProductUpdateRequest $request)
    {
        try {
            $product->category_id = $request->category_id;
            $product->name = $request->name;
            $product->slug = Str::slug($request->name);
            $product->description = $request->description;
            $product->price = $request->price;
            $product->save();

            return $this->successResponse(new ProductResource($product), trans('messages.product_update_success'));
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }

    public function submit(Product $product, ProductSubmitRequest $request)
    {
        try {
            $product->status = 1;
            $product->save();

            return $this->successResponse(new ProductResource($product), trans('messages.product_submitted_success'));
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }

    public function iterate(Product $product, ProductIterateRequest $request)
    {
        try {
            $product = Product::query()->create([
                'parent_id' => $product->id,
                'user_id' => $this->user->id,
                'category_id' => $request->category_id,  
                'name' => $request->name, 
                'slug' => Str::slug($request->name),
                'source_code' => $request->source_code, 
                'description' => $request->description, 
                'price' => $request->price
            ]);

            return $this->successResponse(new ProductResource($product), trans('messages.iteration_store_success'));
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
        $product = Product::with(['category', 'user', 'iterations'=> function($query) {
                $query->with(['category', 'user', 'iterations']);
            }])
            ->where([
                'id' => $id,
                'slug' => $slug
            ])
            ->firstOrFail();
        $product->increment('view_count');

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

