<?php

namespace App\Http\Controllers\Api\Profile;

use App\Models\Cave;
use App\Models\Product;
use App\Http\Resources\Profile\CaveCollection;
use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CaveController extends Controller
{
    use ApiResponser;
    public $user;
 
    public function __construct()
    {
        $this->user = auth('api')->user();
    }
    
    public function index()
    {
        $cave = Cave::where('user_id', $this->user->id)
            ->with(['product.user'])
            ->paginate(10);
        
        return new CaveCollection($cave);
    }

    public function store(Product $product)
    {
        try {
            $cave = Cave::firstOrCreate([
                'user_id' => $this->user->id, 
                'product_id' => $product->id, 
                'type' => 1
            ]);

            return $this->successResponse($cave, trans('messages.cave_store_success'));
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }
}
