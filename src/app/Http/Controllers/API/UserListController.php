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
    const PER_PAGE = 5;

    public function __invoke(Request $request): AnonymousResourceCollection
    {
        $sort = $request->get('sort') ?? '';


        $usersQuery = User::query();

        // NOTE: column search takes precedence over quick search
        //
        if ($this->shouldSearchForColumn($request)) {
            $searchColumn = $request->get('search_col');
            $searchColumnValue = $request->get('search_col_val');
            $columnName = $this->getColumnName($searchColumn);
            $usersQuery->where($columnName, 'like', '%' . $searchColumnValue . '%');
        } else {
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

        return UserResource::collection($usersQuery->select('id', 'first_name', 'last_name', 'user_email', 'created_at')->paginate(SELF::PER_PAGE));
    }

    private function shouldSearchForColumn(Request $request): bool
    {
        if ($request->has('search_col') && $request->has('search_col_val')) {
            $searchColumn = $request->get('search_col');
            $searchColumnValue = $request->get('search_col_val');

            if ((!is_null($searchColumn) && $searchColumn !== "") &&
                (!is_null($searchColumnValue) && $searchColumnValue !== "")
            ) {
                if (in_array($searchColumn, ['fname', 'lname', 'email', 'date'])) {
                    return true;
                }
            }
        }
        return false;
    }

    private function getColumnName(string $requestedColumnName): string
    {
        $columnMappings = [
            'fname' => 'first_name',
            'lname' => 'last_name',
            'email' => 'user_email',
            'date' => 'created_at'
        ];

        if (array_key_exists($requestedColumnName, $columnMappings)) {
            return $columnMappings[$requestedColumnName];
        }

        throw new \Exception('Column name should have existed at this point');
    }
}
