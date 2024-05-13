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
        $perPage = $request->get('perPage') ?? 15;
        $sort = $request->get('sort') ?? [];

        $total = 0;
        $offset = ($page - 1) * $perPage;

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

        if ($request->has('sort')) {
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

        // if ((count($request['sort']) > 0)) {
        //     if (in_array('firstname_desc', $request['sort'])) {
        //         $usersQuery->orderBy('first_name', 'desc');
        //     }
        //     if (in_array('lastname_desc', $request['sort'])) {
        //         $usersQuery->orderBy('last_name', 'desc');
        //     }
        //
        //     if (in_array('email_desc', $request['sort'])) {
        //         $usersQuery->orderBy('user_email', 'desc');
        //     }
        // }
        $page = (int)$request->get('page') ?? 1;

        return UserResource::collection($usersQuery->select('id', 'first_name', 'last_name', 'user_email', 'created_at')->paginate($page));
    }
}
