<?php

declare(strict_types=1);

namespace App\Http\Services;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Services\SearchQueryHandlerInterface;

final class UserSearchQueryHandler implements SearchQueryHandlerInterface
{
    public function __construct(private User $user)
    {
    }

    public function buildQuery(Request $request): Builder
    {
        $usersQuery = $this->user->query();

        // column search takes precedence over quick search
        $usersQuery = $this->buildSearchQueryRequest($request, $usersQuery);

        return $this->buildSortQueryRequest($request, $usersQuery);
    }

    /**
     * If search_col and search request parameter are available,
     * search_col would take precedence
     */
    private function buildSearchQueryRequest(Request $request, Builder $usersQuery): Builder
    {
        if ($this->shouldSearchForColumn($request)) {
            $searchColumn = $request->get('search_col');
            $searchColumnValue = $request->get('search_col_val');
            $columnName = $this->getColumnName($searchColumn);
            $usersQuery->where($columnName, 'like', '%' . $searchColumnValue . '%');
            return $usersQuery;
        }

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
        return $usersQuery;
    }

    private function buildSortQueryRequest(Request $request, Builder $usersQuery): Builder
    {
        if ($request->has('sort')) {
            $sort = $request->get('sort');
            if (!is_null($sort) && $sort !== "") {
                switch ($sort) {
                    case 'fname_desc':
                        $usersQuery->orderBy('first_name', 'desc');
                        break;
                    case 'fname_asc':
                        $usersQuery->orderBy('first_name', 'asc');
                        break;
                    case 'lname_desc':
                        $usersQuery->orderBy('last_name', 'desc');
                        break;
                    case 'lname_asc':
                        $usersQuery->orderBy('last_name', 'asc');
                        break;
                    case 'email_desc':
                        $usersQuery->orderBy('user_email', 'desc');
                        break;
                    case 'email_asc':
                        $usersQuery->orderBy('user_email', 'asc');
                        break;
                    case 'date_asc':
                        $usersQuery->orderBy('created_at', 'asc');
                        break;
                    case 'date_desc':
                        $usersQuery->orderBy('created_at', 'desc');
                        break;
                    default:
                        break;
                }
            }
        }
        return $usersQuery;
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
