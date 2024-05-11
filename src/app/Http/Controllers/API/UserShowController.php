<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use App\Http\Resources\UserResource;

final class UserShowController extends Controller
{
    public function __invoke(int $userId): JsonResponse
    {
        $user = User::query()->find($userId);

        if (!$user) {
            return response()->json(['errors' => 'User not found!'], 400);
        }

        return response()->json([
            'data' => new UserResource($user)
        ], 200);
    }
}
