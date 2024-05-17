<?php

declare(strict_types=1);

namespace App\Http\Services;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Services\SearchQueryHandlerInterface;

final class UserSearchQueryHandler implements SearchQueryHandlerInterface
{
    const PER_PAGE = 10;

    public function __construct(private User $user)
    {
    }

    public function handle(Request $request): array
    {
        $usersQuery = $this->user->query();
        // column search takes precedence over quick search

        if ($this->shouldSearchForColumn($request)) {
            $searchColumn = $request->get('search_col');
            $searchColumnValue = $request->get('search_col_val');
            $columnName = $this->getColumnName($searchColumn);
            $usersQuery = $usersQuery->buildColumnSearchQueryRequest($columnName, $searchColumnValue);
        } else {
            if ($request->has('search')) {
                $search = $request->get('search');
                if (!is_null($search) && $search !== "") {
                    $usersQuery = $usersQuery->buildSearchQueryRequest($search);
                }
            }
        }

        if ($request->has('sort')) {
            $sort = $request->get('sort');
            if (!is_null($sort) && $sort !== "") {
                $sortBy = $this->mapSortColumnName($sort);
                $usersQuery = $usersQuery->buildSortQueryRequest($sortBy);
            }
        }

        $users = $usersQuery
            ->select('id', 'first_name', 'last_name', 'user_email', 'created_at')
            ->paginate(SELF::PER_PAGE);

        $perPage = $users->perPage();

        $currentPage = $users->currentPage();

        $meta = [
            "per_page" => $users->perPage(),
            "to" => ($currentPage) * $perPage,
            "total" => $users->total(),
            "current_page" => $users->currentPage(),
            "from" => ((($currentPage - 1) * $perPage) === 0) ? 1 : (($currentPage - 1) * $perPage),
            "last_page" => $users->lastPage(),
        ];

        return ['data' => $users, 'meta' => $meta];
    }

    private function mapSortColumnName($requestedColumnName): array
    {
        $columnMappings = [
            'fname_desc' => ['columnName' => 'first_name', 'orderBy' => 'desc'],
            'fname_asc' => ['columnName' => 'first_name', 'orderBy' => 'asc'],
            'lname_desc' => ['columnName' => 'last_name', 'orderBy' => 'desc'],
            'lname_asc' => ['columnName' => 'last_name', 'orderBy' => 'asc'],
            'email_desc' => ['columnName' => 'user_email', 'orderBy' => 'desc'],
            'email_asc' => ['columnName' => 'user_email', 'orderBy' => 'asc'],
            'date_desc' => ['columnName' => 'created_at', 'orderBy' => 'desc'],
            'date_asc' => ['columnName' => 'created_at', 'orderBy' => 'asc'],
        ];

        if (array_key_exists($requestedColumnName, $columnMappings)) {
            return $columnMappings[$requestedColumnName];
        }

        throw new \Exception('Column name should have existed at this point');
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
