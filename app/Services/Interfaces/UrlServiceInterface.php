<?php

namespace App\Services\Interfaces;

use App\Models\Url;

interface UrlServiceInterface
{
    public function store(string $originalUrl): Url;

    public function show(string $alias): Url;

    public function formatUrl(string $url): string;

    public function generateUniqueAlias(): string;
}
