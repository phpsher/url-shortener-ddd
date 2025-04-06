<?php

namespace App\Traits;

use App\Exceptions\InternalServerErrorException;
use App\Exceptions\UrlNotFoundException;
use Exception;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

trait HandlesUrlExceptions
{
    public function handleControllerExceptions(callable $callback, $context)
    {
        try {
            return $callback();
        } catch (InvalidArgumentException $e) {
            Log::warning('Invalid URL format provided', [
                'context' => $context,
                'message' => $e->getMessage()
            ]);
            return $this->errorResponse('Invalid URL format', $e->getMessage(), 422);
        } catch (UrlNotFoundException $e) {
            Log::warning('URL not found', [
                'context' => $context,
                'message' => $e->getMessage()
            ]);
            return $this->errorResponse('Not Found', 'URL not found', 404);
        } catch (InternalServerErrorException $e) {
            Log::error('Internal server error', [
                'context' => $context,
                'message' => $e->getMessage()
            ]);
            return $this->errorResponse('Internal Server Error', 'An unexpected error occurred');
        } catch (Exception $e) {
            Log::error('Unexpected error', [
                'context' => $context,
                'message' => $e->getMessage()
            ]);
            return $this->errorResponse('Internal Server Error', 'An unexpected error occurred');
        }
    }

    /**
     * @throws InternalServerErrorException
     */
    public function handleServiceExceptions(callable $callback, string $context): mixed
    {
        try {
            return $callback();
        } catch (InvalidArgumentException $e) {
            Log::warning("Invalid URL format", [
                'context' => $context,
                'message' => $e->getMessage()
            ]);
            throw $e;
        } catch (InternalServerErrorException $e) {
            Log::error("Repository error", [
                'context' => $context,
                'message' => $e->getMessage()
            ]);
            throw $e;
        } catch (Exception $e) {
            Log::error("Unexpected error", [
                'context' => $context,
                'message' => $e->getMessage()
            ]);
            throw new InternalServerErrorException('An unexpected error occurred');
        }
    }
}
