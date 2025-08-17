<?php

namespace App\Infrastructure\Persistence\Url\Repository;

use App\Application\DTOs\Click\CreateClickDTO;
use App\Application\DTOs\Click\ExistsClickDTO;
use App\Application\DTOs\Url\ExistsAliasDTO;
use App\Application\DTOs\Url\SaveUrlDTO;
use App\Application\DTOs\Url\ShowUrlDTO;
use App\Application\Exceptions\InternalServerErrorException;
use App\Domain\Click\Repository\ClickRepositoryInterface;
use App\Domain\Url\Entities\UrlEntity;
use App\Domain\Url\Repositories\UrlRepositoryInterface;
use App\Infrastructure\Persistence\Url\Models\UrlModel;
use Throwable;

readonly final class UrlRepository implements UrlRepositoryInterface
{
    public function __construct(
        private ClickRepositoryInterface $clickRepository
    )
    {
    }

    /**
     * @param SaveUrlDTO $dto
     * @return UrlEntity
     * @throws InternalServerErrorException
     */
    public function save(SaveUrlDTO $dto): UrlEntity
    {
        try {
            $url = UrlModel::create([
                'original_url' => $dto->originalUrl,
                'alias' => $dto->alias,
            ]);

            return new UrlEntity(
                $url->id,
                $url->original_url,
                $url->alias,
                $url->created_at
            );
        } catch (Throwable $exception) {
            throw new InternalServerErrorException($exception->getMessage());
        }
    }

    /**
     * @param ShowUrlDTO $dto
     * @return UrlEntity
     */
    public function show(ShowUrlDTO $dto): UrlEntity
    {
        $url = UrlModel::where('alias', $dto->alias)
            ->firstOrFail();

        $click = $this->clickRepository->exists(
            new ExistsClickDTO(
                $url->id,
                $dto->ip,
            )
        );

        if (!$click) {
            $this->clickRepository->create(
                new CreateClickDTO(
                    $url->id,
                    $dto->ip,
                )
            );
        }

        return new UrlEntity(
            $url->id,
            $url->original_url,
            $url->alias,
            $url->created_at
        );
    }


    /**
     * @param ExistsAliasDTO $dto
     * @return bool
     * @throws InternalServerErrorException
     */
    public function aliasExists(ExistsAliasDTO $dto): bool
    {
        try {
            return UrlModel::where('alias', $dto->alias)
                ->exists();
        } catch (Throwable $exception) {
            throw new InternalServerErrorException($exception->getMessage());
        }
    }
}