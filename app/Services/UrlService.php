<?php

namespace App\Services;

use App\Contracts\Services\UrlServiceContract;
use App\Exceptions\InternalServerErrorException;
use App\Exceptions\UrlNotFoundException;
use App\Models\Click;
use App\Models\Url;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Throwable;

readonly class UrlService implements UrlServiceContract
{
    /**
     * @param string $originalUrl
     * @return Url
     * @throws InternalServerErrorException
     */
    public function store(string $originalUrl): Url
    {
        try {
            $originalUrl = $this->formatUrl($originalUrl);
            $alias = $this->generateUniqueAlias();

            return Url::create([
                'original_url' => $originalUrl,
                'alias' => $alias,
            ]);
        } catch (Throwable $e) {
            throw new InternalServerErrorException($e->getMessage());
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
            $alias = Str::random(4);
            $attempt++;
            $exists = Url::where('alias', $alias)
                ->exists();
        } while ($exists && $attempt < $maxAttempts);

        if ($attempt >= $maxAttempts) {
            throw new InternalServerErrorException('Failed to generate unique alias');
        }

        return $alias;
    }

    /**
     * @param string $alias
     * @param string $ip
     * @return Url
     * @throws InternalServerErrorException
     */
    public function show(
        string $alias,
        string $ip,
    ): Url
    {
        try {
            $url = Url::where('alias', $alias)
                ->firstOrFail();


            $click = Click::where('url_id', $url->id)
                ->where('ip', $ip)
                ->exists();

            if (!$click) {
                Click::create([
                    'url_id' => $url->id,
                    'ip' => $ip
                ]);
            }

            return $url;
        } catch (Throwable $e) {
            throw new InternalServerErrorException($e->getMessage());
        }
    }
}
