<?php

namespace App\Application\DTOs\Url;

class SaveUrlDTO
{
    public function __construct(
        public string $originalUrl,
        public ?string $alias = null,
    )
    {
    }
}