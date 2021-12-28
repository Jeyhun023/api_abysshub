<?php

namespace App\Http\Controllers\Api\Profile;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Activity;
use App\Http\Resources\Profile\Inventory\InventoryHistoryCollection;
use App\Http\Resources\Profile\Inventory\InventoryCollection;
use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class InventoryController extends Controller
{
    use ApiResponser;
    public $user;
 
    public function __construct()
    {
        $this->user = auth('api')->user();
    }
    
    public function index()
    {
        $inventory = Inventory::where('user_id', $this->user->id)
            ->with(['product.user'])
            ->paginate(10);
        
        return new InventoryCollection($inventory);
    }

    public function store(Product $product)
    {
        try {
            $inventory = Inventory::firstOrCreate([
                'user_id' => $this->user->id, 
                'product_id' => $product->id, 
                'type' => 1
            ]);

            if($inventory->wasRecentlyCreated){
                return $this->successResponse($inventory, trans('messages.inventory_store_success'));
            }
            return $this->errorResponse(["failed" => [trans('messages.inventory_already_exist')] ]);
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }
    
    public function delete(Product $product)
    {
        try {
            $inventory = Inventory::where([
                'user_id' => $this->user->id, 
                'product_id' => $product->id, 
            ])->delete();
            if($inventory){
                return $this->successResponse($inventory, trans('messages.inventory_delete_success'));
            }
            return $this->errorResponse(["failed" => [trans('messages.inventory_not_exist')] ]);
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }

    public function history()
    {
        $history = Activity::where([
            'causer_id' => $this->user->id,
            'causer_type' => 'App\Models\User',
            'event' => 'show',
            'subject_type' => 'App\Models\Product',
        ])->with('product.user')->paginate(10);

        return $this->successResponse(new InventoryHistoryCollection($history), null);
    }
}
