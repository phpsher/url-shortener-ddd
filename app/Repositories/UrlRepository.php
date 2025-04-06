<?php

namespace App\Repositories;

use App\Exceptions\InternalServerErrorException;
use App\Exceptions\UrlNotFoundException;
use App\Models\Url;
use App\Repositories\Interfaces\UrlRepositoryInterface;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class UrlRepository implements UrlRepositoryInterface
{
    /**
     * @throws InternalServerErrorException
     * @throws InvalidArgumentException
     */
    public function create(string $originalUrl, string $alias): Url
    {
        try {
            return Url::create([
                'original_url' => $originalUrl,
                'alias' => $alias,
            ]);
        } catch (QueryException $e) {
            Log::error("Database error in UrlRepository::create: " . $e->getMessage(), [
                'original_url' => $originalUrl,
                'alias' => $alias,
                'exception' => $e
            ]);
            throw new InternalServerErrorException('Failed to create shortened URL');
        } catch (Exception $e) {
            Log::error("Unexpected error in UrlRepository::create: " . $e->getMessage(), [
                'original_url' => $originalUrl,
                'alias' => $alias,
                'exception' => $e
            ]);
            throw new InternalServerErrorException('An unexpected error occurred');
        }
    }

    /**
     * @throws InternalServerErrorException|UrlNotFoundException
     */
    public function findByAlias(string $alias): Url
    {
        try {
            $url = Url::where('alias', $alias)->first();

            if (!$url) {
                Log::warning("URL not found", ['alias' => $alias]);
                throw new UrlNotFoundException("URL not found for alias: $alias");
            }

            return $url;
        } catch (QueryException $e) {
            Log::error("Database error in UrlRepository::findByAlias: " . $e->getMessage(), [
                'alias' => $alias,
                'exception' => $e
            ]);
            throw new InternalServerErrorException('Failed to retrieve URL');
        } catch (Exception $e) {
            Log::error("Unexpected error in UrlRepository::findByAlias: " . $e->getMessage(), [
                'alias' => $alias,
                'exception' => $e
            ]);
            throw new InternalServerErrorException('An unexpected error occurred');
        }
    }

    /**ii
     * @throws InternalServerErrorException
     * @throws InvalidArgumentException
     */
    public function aliasExists(string $alias): bool
    {
        try {
            return Url::where('alias', $alias)->exists();
        } catch (QueryException $e) {
            Log::error("Database error in UrlRepository::aliasExists: " . $e->getMessage(), [
                'alias' => $alias,
                'exception' => $e
            ]);
            throw new InternalServerErrorException('Failed to check alias existence');
        } catch (Exception $e) {
            Log::error("Unexpected error in UrlRepository::aliasExists: " . $e->getMessage(), [
                'alias' => $alias,
                'exception' => $e
            ]);
            throw new InternalServerErrorException('An unexpected error occurred');
        }
    }
}

