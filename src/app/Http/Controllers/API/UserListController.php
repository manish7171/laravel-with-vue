<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Resources\UserResource;
use App\Http\Responses\CollectionResponse;
use App\Http\Services\SearchQueryHandlerInterface;
use \Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

final class UserListController extends Controller
{

    public function __invoke(Request $request, SearchQueryHandlerInterface $queryHandler): CollectionResponse
    {
        $usersList = $queryHandler->handle($request);

        return new CollectionResponse(UserResource::collection($usersList['data']), $usersList['meta'], Response::HTTP_OK);
    }

    /**
     *{
    "data": [
        {
            "id": 15,
            "firstName": "Reba",
            "lastName": "Streich",
            "email": "daugherty.polly@example.net",
            "createdAt": "2024-05-14"
        },
    ],
    "links": {
        "first": "http://localhost:8000/api/users?page=1",
        "last": "http://localhost:8000/api/users?page=3",
        "prev": null,
        "next": "http://localhost:8000/api/users?page=2"
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 3,
        "links": [
            {
                "url": null,
                "label": "&laquo; Previous",
                "active": false
            },
            {
                "url": "http://localhost:8000/api/users?page=1",
                "label": "1",
                "active": true
            },
            {
                "url": "http://localhost:8000/api/users?page=2",
                "label": "2",
                "active": false
            },
            {
                "url": "http://localhost:8000/api/users?page=3",
                "label": "3",
                "active": false
            },
            {
                "url": "http://localhost:8000/api/users?page=2",
                "label": "Next &raquo;",
                "active": false
            }
        ],
        "path": "http://localhost:8000/api/users",
        "per_page": 10,
        "to": 10,
        "total": 24
    }
}
     *
     *
     * */
}
