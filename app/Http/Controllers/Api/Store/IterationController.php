<?php

namespace App\Http\Controllers\Api\Store;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\Store\ProductIterationResource;
use App\Http\Requests\Api\Store\Iteration\ProductIterateRequest;

class IterationController extends Controller
{
    use ApiResponser;
    public $user;
    
    public function __construct()
    {
        $this->user = auth('api')->user();
    }

    public function store(Product $product, ProductIterateRequest $request)
    {
        try {
            $file = 'temporary/' .md5(time()). '.py';
            Storage::disk('products')->put($file, $request->source_code);
            
            $iteration = ProductIteration::query()->create([
                'user_id' => $this->user->id,
                'product_id' => $product->id,  
                'name' => $request->name, 
                'file' => $file,
                'slug' => Str::slug($request->name),
                'note' => $request->note
            ]);

            return $this->successResponse(new ProductIterationResource($iteration), trans('messages.iteration_store_success'));
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }

}
