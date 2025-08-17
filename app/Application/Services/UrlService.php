<?php

namespace App\Application\Services;

use App\Application\DTOs\Url\ExistsAliasDTO;
use App\Application\DTOs\Url\FormatUrlDTO;
use App\Application\DTOs\Url\SaveUrlDTO;
use App\Application\DTOs\Url\ShowUrlDTO;
use App\Application\Exceptions\InternalServerErrorException;
use App\Domain\Url\Entities\UrlEntity;
use App\Domain\Url\Repositories\UrlRepositoryInterface;
use App\Domain\Url\Services\UrlServiceInterface;
use Illuminate\Support\Str;
use InvalidArgumentException;

readonly class UrlService implements UrlServiceInterface
{

    /**
     * @param UrlRepositoryInterface $urlRepository
     */
    public function __construct(
        private UrlRepositoryInterface $urlRepository
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
        $dto->alias = $this->generateUniqueAlias();
        return $this->urlRepository->save($dto);
    }


    /**
     * @param ShowUrlDTO $dto
     * @return UrlEntity
     */
    public function show(ShowUrlDTO $dto): UrlEntity
    {
        return $this->urlRepository->show($dto);
    }

    /**
     * @param FormatUrlDTO $dto
     * @return string
     */
    public function formatUrl(FormatUrlDTO $dto): string
    {
        $url = trim($dto->url);

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('Invalid URL format');
        }

        if (!preg_match('/^https?:\/\//', $url)) {
            $url = 'https://' . $url;
        }

        return $url;
    }

    /**
     * @return string
     * @throws InternalServerErrorException
     */
    public function generateUniqueAlias(): string
    {
        $maxAttempts = 5;
        $attempt = 0;

        do {
            $alias = Str::random(4);
            $attempt++;

            $exists = $this->urlRepository->aliasExists(
                new ExistsAliasDTO(
                    alias: $alias
                )
            );

        } while ($exists && $attempt < $maxAttempts);

        if ($attempt >= $maxAttempts) {
            throw new InternalServerErrorException('Failed to generate unique alias');
        }

        return $alias;
    }
}