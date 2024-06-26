<?php

declare(strict_types=1);

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;

class UserBuilder extends Builder
{
    public function buildColumnSearchQueryRequest($columnName, $searchColumnValue): Builder
    {
        return $this->where($columnName, 'like', '%' . $searchColumnValue . '%');
    }

    public function buildSearchQueryRequest($search): Builder
    {
        return $this->where(function ($query) use ($search) {
            $query->orWhere('first_name', 'like', '%' . $search . '%')
                ->orWhere('last_name', 'like', '%' . $search . '%')
                ->orWhere('user_email', 'like', '%' . $search . '%')
                ->orWhere('created_at', 'like', '%' . $search . '%');
        });
    }

    public function buildSortQueryRequest($sortRequest): Builder
    {
        return $this->orderBy($sortRequest['columnName'], $sortRequest['orderBy']);
    }
}
