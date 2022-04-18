<?php

namespace App\Http\Controllers\Api\Store;

use App\Models\Image;
use App\Models\Rating;
use App\Models\Product;
use App\Models\ProductIteration;
use App\Events\StoreElasticEvent;
use App\Http\Resources\Store\RatingResource;
use App\Http\Resources\Store\ProductResource;
use App\Http\Resources\Store\ProductCollection;
use App\Http\Requests\Api\Store\Product\RatingRequest;
use App\Http\Requests\Api\Store\Product\FullRatingRequest;
use App\Http\Requests\Api\Store\Product\ProductUpdateRequest;
use App\Http\Requests\Api\Store\Product\ProductDeleteRequest;

use File;
use Illuminate\Support\Str;
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

            $this->imagesApply($product, $request);

            if($request->input('submit')){
                if($request->draft){
                    $this->plagiarismCheck($product);
                }
                return $this->submit($product, $request);
            }
            if($request->draft){
                return $this->plagiarismCheck($product);
            }
            
            return $this->successResponse(new ProductResource($product), trans('messages.product_update_success'));
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }

    public function imageUpload(Product $product, Request $request)
    {
        $validated = $request->validate([
            'images' => 'required|array|max:10',
            'images.*' => 'mimes:jpeg,png,gif,jpg'
        ]);
        if($request->hasfile('images'))
        {
            $product->images()->delete();
            // File::deleteDirectory(storage_path('public/products/'.$product->id));
            foreach($request->file('images') as $key => $image)
            {
                $imageName = Str::random(40).'.'.$image->extension();
                $imagePath = $image->store('public/products/'.$product->id.'/'.$imageName);
                $uploadedImages[] = Image::create([
                    'imageable_type' => Product::class,
                    'imageable_id' => $product->id,
                    'title' => $image->getClientOriginalName(),
                    'path' => $imagePath,
                    'order_id' => $key
                ]);
            }
            return $this->successResponse($uploadedImages, null);
        }
        return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
    }

    private function imagesApply(Product $product, $request)
    {
        if($request->added){
            foreach($request->added as $key => $value){
                $image = $request->file('key')->store('product/'.$prodcut->id.$value.'salamagdamliqaqas.jpg');
            }
        }
    }

    private function submit(Product $product, $request)
    {
        if(!$product->name || !$product->tags || !isset(json_decode($product->description)->description) || 
        !isset(json_decode($product->description)->applicability) || !isset(json_decode($product->description)->problemFormulation)){
            return $this->errorResponse(["failed" => [trans('messages.store_fill_details')] ]);
        }
        if($product->draft !== null && $product->is_plagiat){
            return $this->errorResponse(["plagiarismDetected" => [trans('messages.plagiat_error')]]);
        }
        if($product->draft !== null){
            $product->file = $product->draft;
            $product->draft = null;
            $product->is_plagiat = true;
        }
        $product->is_submitted = true;
        $product->save();
        event(new StoreElasticEvent($product));

        return $this->successResponse(new ProductResource($product), trans('messages.product_submitted_success'));
    }

    private function plagiarismCheck(Product $product)
    {
        try {
            $response = Http::get('https://django.abysshub.com/api/plagiarism/check/'.$product->id);
            if($response->failed()){
                return $this->errorResponse(["plagiarismDetected" => [trans('messages.plagiat_error')] ]);
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

    public function search()
    {
        $products = Product::with('user')->withCount(['iterations', 'linkedProducts'])
            ->orderByDesc('id')->paginate(10);
        return $this->successResponse(new ProductCollection($products), null);
    }
}