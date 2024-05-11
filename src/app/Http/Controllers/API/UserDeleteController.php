<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

final class UserDeleteController extends Controller
{
    public function __invoke(int $userId): JsonResponse
    {
        $user = User::query()->find($userId);

        if (!$user) {
            return response()->json(['errors' => 'User not found!'], 400);
        }

        $user->delete();

        return response()->json(null, 204);
    }
}
