<?php

namespace App\Contracts\Services;

use App\Models\Url;

interface UrlServiceContract
{
    public function store(string $originalUrl): Url;

    public function show(string $alias): Url;

    public function formatUrl(string $url): string;

    public function generateUniqueAlias(): string;
}
