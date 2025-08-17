<?php

namespace App\Infrastructure\Persistence\Click\Repository;

use App\Application\DTOs\Click\CreateClickDTO;
use App\Application\DTOs\Click\ExistsClickDTO;
use App\Application\Exceptions\InternalServerErrorException;
use App\Domain\Click\Entities\ClickEntity;
use App\Infrastructure\Persistence\Click\Models;
use App\Domain\Click\Repository\ClickRepositoryInterface;
use App\Infrastructure\Persistence\Click\Models\ClickModel;
use Throwable;

class ClickRepository implements ClickRepositoryInterface
{

    /**
     * @param ExistsClickDTO $dto
     * @return bool
     * @throws InternalServerErrorException
     */
    public function exists(ExistsClickDTO $dto): bool
    {
        try {
            return ClickModel::where('url_id', $dto->url_id)
                ->where('ip', $dto->ip)
                ->exists();
        } catch (Throwable $exception) {
            throw new InternalServerErrorException($exception->getMessage());
        }
    }

    /**
     * @param CreateClickDTO $dto
     * @return ClickEntity
     * @throws InternalServerErrorException
     */
    public function create(CreateClickDTO $dto): ClickEntity
    {
        try {
            $click = ClickModel::create([
                'url_id' => $dto->url_id,
                'ip' => $dto->ip,
            ]);

            return new ClickEntity(
                $click->id,
                $dto->url_id,
                $dto->ip,
                $click->created_at
            );
        } catch (Throwable $exception) {
            throw new InternalServerErrorException($exception->getMessage());
        }
    }
}