<?php

declare(strict_types=1);

namespace App\Http\Responses;

use App\Http\Factories\HeaderFactory;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

final readonly class CollectionResponse implements Responsable
{
    public function __construct(
        private readonly AnonymousResourceCollection $data,
        private readonly array $meta,
        private int $status = Response::HTTP_OK
    ) {
    }
    public function toResponse($request): Response
    {
        $data = ['data' => $this->data, 'meta' => $this->meta];
        return new JsonResponse(
            data: $data,
            status: $this->status
        );
    }
}
