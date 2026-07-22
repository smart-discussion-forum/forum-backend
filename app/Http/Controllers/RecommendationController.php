<?php

namespace App\Http\Controllers;

use App\Services\RecommendationService;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    public function index(Request $request, RecommendationService $recommendations)
    {
        $personalized = $recommendations->recommendedTopicsFor($request->user());
        $trending = $recommendations->trendingTopicsFor();

        return view('recommendations.index', compact('personalized', 'trending'));
    }

    public function apiIndex(Request $request, RecommendationService $recommendations)
    {
        return response()->json($recommendations->combinedPayload($request->user()));
    }
}
