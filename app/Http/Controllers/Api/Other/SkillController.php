<?php

namespace App\Http\Controllers\Api\Other;

use App\Models\Skill;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;

class SkillController extends Controller
{
    use ApiResponser;

    public function search($query)
    {
        try {
            $skills = Skill::where('name', 'LIKE', '%' . $query . '%')->get();
            
            return $this->successResponse($skills, null);
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }
}
