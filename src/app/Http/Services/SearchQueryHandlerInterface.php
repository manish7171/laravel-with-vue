<?php

declare(strict_types=1);

namespace App\Http\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

interface SearchQueryHandlerInterface
{
    public function buildQuery(Request $request): Builder;
}
