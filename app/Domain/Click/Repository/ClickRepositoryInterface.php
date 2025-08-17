<?php

namespace App\Domain\Click\Repository;

use App\Application\DTOs\Click\CreateClickDTO;
use App\Application\DTOs\Click\ExistsClickDTO;
use App\Domain\Click\Entities\ClickEntity;

interface ClickRepositoryInterface
{
    public function exists(ExistsClickDTO $dto): bool;

    public function create(CreateClickDTO $dto): ClickEntity;
}