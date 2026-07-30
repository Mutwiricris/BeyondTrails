<?php

namespace App\Http\Controllers\Api\V1\Discover;

use App\Http\Controllers\Controller;
use App\Models\Challenge;
use App\Models\UserChallenge;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChallengeController extends Controller
{
    /**
     * Get active challenges list.
     *
     * GET /api/v1/discover/challenges
     */
    public function index(Request $request): JsonResponse
    {
        $challenges = Challenge::all();
        $user = $request->user();

        $userChallenges = $user
            ? UserChallenge::where('user_id', $user->id)->get()->keyBy('challenge_id')
            : collect();

        $formatted = $challenges->map(function ($c) use ($userChallenges) {
            $userProgress = $userChallenges->get($c->id);

            return [
                'id'            => $c->id,
                'name'          => $c->name,
                'description'   => $c->description,
                'points_reward' => $c->points_reward,
                'icon'          => $c->icon ?? '🏆',
                'difficulty'    => $c->difficulty,
                'type'          => $c->type,
                'target_value'  => $c->target_value,
                'status'        => $userProgress ? $userProgress->status : 'not_started',
                'progress'      => $userProgress ? $userProgress->progress : 0,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $formatted,
        ]);
    }
}
