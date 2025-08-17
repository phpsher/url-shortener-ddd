<?php

namespace App\Application\DTOs\Url;

class FormatUrlDTO
{
    public function __construct(
        public string $url,
    )
    {
    }
}