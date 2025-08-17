<?php

namespace App\Domain\Url\Repositories;

use App\Application\DTOs\Url\ExistsAliasDTO;
use App\Application\DTOs\Url\SaveUrlDTO;
use App\Application\DTOs\Url\ShowUrlDTO;
use App\Domain\Url\Entities\UrlEntity;
use App\Infrastructure\Persistence\Url\Models\UrlModel;

interface UrlRepositoryInterface
{
    public function save(SaveUrlDTO $dto): UrlEntity;

    public function show(ShowUrlDTO $dto): UrlEntity;

    public function aliasExists(ExistsAliasDTO $dto): bool;
}