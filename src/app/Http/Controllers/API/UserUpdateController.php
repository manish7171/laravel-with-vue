<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

final class UserUpdateController extends Controller
{
    public function __invoke(Request $request, int $userId): JsonResponse
    {
        $validated = $request->validate([
            'firstname' => 'required|max:255',
            'lastname'  => 'required|max:255',
            'email'     => 'required|unique:users,user_email,' . $userId . '|max:255',
        ]);

        $user = User::query()->find($userId);;

        if (!$user) {
            return response()->json(['errors' => 'User not found!'], 400);
        }

        $user->update([
            "first_name" => $validated['firstname'],
            "last_name" => $validated['lastname'],
            "user_email" => $validated['email'],
        ]);

        return response()->json([
            'data' => new UserResource($user)
        ], 201);
    }
}
