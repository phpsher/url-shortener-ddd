<?php

namespace App\Services;

use App\Exceptions\InternalServerErrorException;
use App\Exceptions\UrlNotFoundException;
use App\Models\Url;
use App\Repositories\Interfaces\UrlRepositoryInterface;
use App\Services\Interfaces\UrlServiceInterface;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use InvalidArgumentException;

class UrlService implements UrlServiceInterface
{
    protected UrlRepositoryInterface $urlRepository;

    public function __construct(UrlRepositoryInterface $urlRepository)
    {
        $this->urlRepository = $urlRepository;
    }

    /**
     * @throws InternalServerErrorException
     * @throws InvalidArgumentException
     */
    public function store(string $originalUrl): Url
    {
        try {
            $originalUrl = $this->formatUrl($originalUrl);
            $alias = $this->generateUniqueAlias();

            return $this->urlRepository->create($originalUrl, $alias);
        } catch (InvalidArgumentException $e) {
            Log::warning("Invalid URL format", [
                'url' => $originalUrl,
                'message' => $e->getMessage()
            ]);
            throw $e;
        } catch (InternalServerErrorException $e) {
            Log::error("Repository error while storing URL", [
                'url' => $originalUrl,
                'message' => $e->getMessage()
            ]);
            throw $e;
        } catch (Exception $e) {
            Log::error("Unexpected error while storing URL", [
                'url' => $originalUrl,
                'message' => $e->getMessage()
            ]);
            throw new InternalServerErrorException('Failed to create short URL');
        }
    }

    /**
     * @throws UrlNotFoundException
     * @throws InternalServerErrorException
     */
    public function show(string $alias): Url
    {
        try {
            return $this->urlRepository->findByAlias($alias);
        } catch (UrlNotFoundException $e) {
            Log::warning("URL not found", ['alias' => $alias]);
            throw $e;
        } catch (InternalServerErrorException $e) {
            Log::error("Repository error while retrieving URL", [
                'alias' => $alias,
                'message' => $e->getMessage()
            ]);
            throw $e;
        } catch (Exception $e) {
            Log::error("Unexpected error while retrieving URL", [
                'alias' => $alias,
                'message' => $e->getMessage()
            ]);
            throw new InternalServerErrorException('Failed to retrieve URL');
        }
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
            try {
                $alias = Str::random(4);
                $exists = $this->urlRepository->aliasExists($alias);
                $attempt++;
            } catch (InternalServerErrorException $e) {
                Log::error("Failed to check alias existence", [
                    'alias' => $alias,
                    'attempt' => $attempt,
                    'message' => $e->getMessage()
                ]);
                throw $e;
            }
        } while ($exists && $attempt < $maxAttempts);

        if ($attempt >= $maxAttempts) {
            Log::error("Max attempts reached while generating unique alias");
            throw new InternalServerErrorException('Failed to generate unique alias');
        }

        return $alias;
    }
}
