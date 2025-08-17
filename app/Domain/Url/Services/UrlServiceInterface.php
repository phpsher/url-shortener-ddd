<?php

namespace App\Domain\Url\Services;

use App\Application\DTOs\Url\FormatUrlDTO;
use App\Application\DTOs\Url\SaveUrlDTO;
use App\Application\DTOs\Url\ShowUrlDTO;
use App\Domain\Url\Entities\UrlEntity;
use App\Infrastructure\Persistence\Url\Models\UrlModel;

interface UrlServiceInterface
{
    public function save(SaveUrlDTO $dto): UrlEntity;

    public function show(ShowUrlDTO $dto): UrlEntity;

    public function formatUrl(FormatUrlDTO $dto): string;

    public function generateUniqueAlias(): string;
}
