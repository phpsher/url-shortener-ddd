<?php

namespace App\Services;

use App\Exceptions\InternalServerErrorException;
use App\Models\Url;
use App\Repositories\Interfaces\UrlRepositoryInterface;
use App\Services\Interfaces\UrlServiceInterface;
use App\Traits\HandlesUrlExceptions;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use InvalidArgumentException;

readonly class UrlService implements UrlServiceInterface
{
    use HandlesUrlExceptions;
    public function __construct(
        private UrlRepositoryInterface $urlRepository
    )
    {
    }

    /**
     * @throws InternalServerErrorException
     * @throws InvalidArgumentException
     */
    public function store(string $originalUrl): Url
    {
        $originalUrl = $this->formatUrl($originalUrl);
        $alias = $this->generateUniqueAlias();

        return $this->handleServiceExceptions(function () use ($originalUrl, $alias) {
            return $this->urlRepository->create($originalUrl, $alias);
        }, $originalUrl);
    }

    /**
     * @throws InternalServerErrorException
     */
    public function show(string $alias): Url
    {
        return $this->handleServiceExceptions(function () use ($alias) {
            return $this->urlRepository->findByAlias($alias);
        }, $alias);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function formatUrl(string $url): string
    {
        $url = trim($url);

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('Invalid URL format');
        }

        if (!preg_match('/^https?:\/\//', $url)) {
            $url = 'https://' . $url;
        }

        return $url;
    }

    /**
     * @throws InternalServerErrorException
     */
    public function generateUniqueAlias(): string
    {
        $maxAttempts = 5;
        $attempt = 0;

        do {
            $alias = Str::random(4);
            $attempt++;
            $exists = $this->handleServiceExceptions(function () use ($alias) {
                return $this->urlRepository->aliasExists($alias);
            }, $alias);
        } while ($exists && $attempt < $maxAttempts);

        if ($attempt >= $maxAttempts) {
            Log::error("Max attempts reached while generating unique alias");
            throw new InternalServerErrorException('Failed to generate unique alias');
        }

        return $alias;
    }


}
