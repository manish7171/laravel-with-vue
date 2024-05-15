<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Resources\UserResource;
use App\Http\Services\SearchQueryHandlerInterface;
use \Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class UserListController extends Controller
{
    const PER_PAGE = 10;

    public function __invoke(Request $request, SearchQueryHandlerInterface $queryHandler): AnonymousResourceCollection
    {
        $usersQuery = $queryHandler->buildQuery($request);

        return UserResource::collection(
            $usersQuery
                ->select('id', 'first_name', 'last_name', 'user_email', 'created_at')
                ->paginate(SELF::PER_PAGE)
        );
    }
}
