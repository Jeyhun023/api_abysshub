<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Resources\Profile\Library\LibraryHistoryCollection;
use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;

class LibraryController extends Controller
{
    use ApiResponser;
    public $user;
    
    public function __construct()
    {
        $this->user = auth('api')->user();
    }
        
    public function history()
    {
        $history = Activity::where([
            'causer_id' => $this->user->id,
            'causer_type' => 'App\Models\User',
            'event' => 'show',
            'subject_type' => 'App\Models\Thread',
        ])->with('subject.user')->paginate(10);

        return $this->successResponse(new LibraryHistoryCollection($history), null);
    }
}
