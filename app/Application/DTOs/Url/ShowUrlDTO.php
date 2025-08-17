<?php

namespace App\Application\DTOs\Url;

class ShowUrlDTO
{
    public function __construct(
        public string $alias,
        public string $ip,
    )
    {
    }
}