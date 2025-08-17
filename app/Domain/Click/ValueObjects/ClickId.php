<?php

namespace App\Domain\Click\ValueObjects;

class ClickId
{
    public function __construct(
        private string $id,
    )
    {
        if (empty($this->id)) {
            throw new \InvalidArgumentException('Id cannot be empty');
        }
    }

    public function value(): string
    {
        return $this->id;
    }
}