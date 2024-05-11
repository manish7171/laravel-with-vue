<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

final class UserCreateController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'firstName' => 'required|max:255',
            'lastName'  => 'required|max:255',
            'email'     => 'required|unique:users,user_email|max:255',
        ]);

        $newUser = new User();

        $newUser->first_name = $validated['firstName'];
        $newUser->last_name  = $validated['lastName'];
        $newUser->user_email = $validated['email'];

        $newUser->save();

        return response()->json([
            'data' => new UserResource($newUser)
        ], 201);
    }
}
