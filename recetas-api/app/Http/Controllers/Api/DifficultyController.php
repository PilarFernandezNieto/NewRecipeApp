<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DifficultyResource;
use App\Models\Difficulty;
use Illuminate\Http\JsonResponse;


class DifficultyController extends Controller
{
    public function index(): JsonResponse
    {
        $difficulties = Difficulty::all();

        return response()->json(DifficultyResource::collection($difficulties));
    }
}
