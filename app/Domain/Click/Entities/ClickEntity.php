<?php

namespace App\Domain\Click\Entities;

use App\Domain\Click\ValueObjects\ClickId;
use DateTimeImmutable;
use DateTimeInterface;

class ClickEntity
{
    public function __construct(
        private string $id,
        private string $url_id,
        private string $ip,
        private DateTimeInterface $created_at,
    )
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getUrlId(): string
    {
        return $this->url_id;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->created_at;
    }

    public function setUrlId(string $url_id): void
    {
        $this->url_id = $url_id;
    }

    public function setIp(string $ip): void
    {
        $this->ip = $ip;
    }

    public function setCreatedAt(DateTimeImmutable $created_at): void
    {
        $this->created_at = $created_at;
    }
}
