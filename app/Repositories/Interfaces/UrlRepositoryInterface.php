<?php

namespace App\Repositories\Interfaces;

use App\Models\Url;

interface UrlRepositoryInterface
{
    public function create(string $originalUrl, string $alias): Url;

    public function findByAlias(string $alias): Url;

    public function aliasExists(string $alias): bool;
}
