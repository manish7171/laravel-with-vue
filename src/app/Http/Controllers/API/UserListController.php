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
        $page = $request->get('page') ?? 1;
        $perPage = $request->get('perPage') ?? 5;
        $sort = $request->get('sort') ?? '';

        $total = 0;
        $offset = ($page - 1) * $perPage;

        $usersQuery = User::query();
        if ($request->has('search')) {
            $search = $request->get('search');
            if (!is_null($search) && $search !== "") {
                $usersQuery->where(function ($query) use ($search) {
                    $query->orWhere('first_name', 'like', '%' . $search . '%')
                        ->orWhere('last_name', 'like', '%' . $search . '%')
                        ->orWhere('user_email', 'like', '%' . $search . '%')
                        ->orWhere('created_at', 'like', '%' . $search . '%');
                });
            }
        }

        if ($request->has('sort')) {
            $sort = $request->get('sort');
            if (!is_null($sort) && $sort !== "") {
                if ('fname_desc' == $request->get('sort')) {
                    $usersQuery->orderBy('first_name', 'desc');
                }
                if ('fname_asc' == $request->get('sort')) {
                    $usersQuery->orderBy('first_name', 'asc');
                }

                if ('lname_desc' == $request->get('sort')) {
                    $usersQuery->orderBy('last_name', 'desc');
                }

                if ('lname_asc' == $request->get('sort')) {
                    $usersQuery->orderBy('last_name', 'asc');
                }

                if ('email_desc' == $request->get('sort')) {
                    $usersQuery->orderBy('user_email', 'desc');
                }

                if ('email_asc' == $request->get('sort')) {
                    $usersQuery->orderBy('user_email', 'asc');
                }

                if ('email_desc' == $request->get('sort')) {
                    $usersQuery->orderBy('user_email', 'desc');
                }

                if ('date_asc' == $request->get('sort')) {
                    $usersQuery->orderBy('user_email', 'asc');
                }

                if ('date_desc' == $request->get('sort')) {
                    $usersQuery->orderBy('created_at', 'desc');
                }
            }
        }

        $page = (int)$request->get('page') ?? 1;

        return UserResource::collection($usersQuery->select('id', 'first_name', 'last_name', 'user_email', 'created_at')->paginate($perPage));
    }
}
