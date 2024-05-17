<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Resources\UserResource;
use App\Http\Responses\CollectionResponse;
use App\Http\Services\SearchQueryHandlerInterface;
use Symfony\Component\HttpFoundation\Response;

final class UserListController extends Controller
{

    public function __invoke(Request $request, SearchQueryHandlerInterface $queryHandler): CollectionResponse
    {
        $usersList = $queryHandler->handle($request);

        return new CollectionResponse(UserResource::collection($usersList['data']), $usersList['meta'], Response::HTTP_OK);
    }
}
