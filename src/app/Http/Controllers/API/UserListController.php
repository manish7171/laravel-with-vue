<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Resources\UserResource;
use \Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class UserListController extends Controller
{
    public function __invoke(Request $request): AnonymousResourceCollection
    {
        $usersQuery = User::query();

        if ($request->has('fname')) {
            $usersQuery->where('first_name', 'like', '%' . $request->get('fname') . '%');
        }

        if ($request->has('lname')) {
            $usersQuery->where('last_name', 'like', '%' . $request->get('lname') . '%');
        }

        if ($request->has('email')) {
            $usersQuery->where('user_email', $request->get('email'));
        }

        $page = (int)$request->get('page') ?? 1;

        return UserResource::collection($usersQuery->paginate($page));
    }
}
