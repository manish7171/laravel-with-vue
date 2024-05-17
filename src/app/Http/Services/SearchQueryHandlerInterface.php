<?php

declare(strict_types=1);

namespace App\Http\Services;

use Illuminate\Http\Request;

interface SearchQueryHandlerInterface
{
    public function handle(Request $request): array;
}
