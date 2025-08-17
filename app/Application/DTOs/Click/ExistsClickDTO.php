<?php

namespace App\Application\DTOs\Click;

class ExistsClickDTO
{
    public function __construct(
        public string $url_id,
        public string $ip,
    )
    {
    }
}