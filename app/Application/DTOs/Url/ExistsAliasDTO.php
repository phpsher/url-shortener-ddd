<?php

namespace App\Application\DTOs\Url;

class ExistsAliasDTO
{
    public function __construct(
        public string $alias,
    )
    {
    }
}