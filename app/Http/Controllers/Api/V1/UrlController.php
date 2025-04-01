<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\InternalServerErrorException;
use App\Exceptions\UrlNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUrlRequest;
use App\Services\Interfaces\UrlServiceInterface;
use App\Services\UrlService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="URL Shortener API",
 *     description="API for shortening URLs"
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="URL Shortener API Server"
 * )
 *
 * @OA\Tag(
 *     name="URLs",
 *     description="URL shortening operations"
 * )
 */
class UrlController extends Controller
{
    protected UrlServiceInterface $urlService;

    public function __construct(UrlService $urlService)
    {
        $this->urlService = $urlService;
    }

    /**
     * @OA\Post(
     *     path="/api/v1/urls",
     *     operationId="storeUrl",
     *     summary="Create a shortened URL",
     *     tags={"URLs"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"original_url"},
     *             @OA\Property(property="original_url", type="string", format="url", example="https://example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="URL shortened successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="original_url", type="string", example="https://example.com"),
     *             @OA\Property(property="short_url", type="string", example="http://localhost/abc123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid URL format",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Invalid URL format"),
     *             @OA\Property(property="message", type="string", example="The URL format is invalid")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Internal Server Error"),
     *             @OA\Property(property="message", type="string", example="An unexpected error occurred")
     *         )
     *     )
     * )
     */
    public function store(StoreUrlRequest $request): JsonResponse
    {
        try {
            $urlData = $this->urlService->store($request->validated('original_url'));

            return response()->json([
                'original_url' => $urlData->original_url,
                'short_url' => env('APP_URL') . $urlData->alias,
            ]);
        } catch (InvalidArgumentException $e) {
            Log::warning('Invalid URL format provided', [
                'url' => $request->validated('original_url'),
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Invalid URL format',
                'message' => $e->getMessage()
            ], 422);
        } catch (InternalServerErrorException $e) {
            Log::error('Failed to create short URL', [
                'url' => $request->validated('original_url'),
                'message' => $e->getMessage()
            ]);

            return $e->render();
        } catch (Exception $e) {
            Log::error('Unexpected error while creating short URL', [
                'url' => $request->validated('original_url'),
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Internal Server Error',
                'message' => 'An unexpected error occurred'
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/{alias}",
     *     operationId="showUrl",
     *     summary="Redirect to original URL",
     *     tags={"URLs"},
     *     @OA\Parameter(
     *         name="alias",
     *         in="path",
     *         required=true,
     *         description="Short URL alias",
     *         @OA\Schema(type="string", example="abc123")
     *     ),
     *     @OA\Response(
     *         response=302,
     *         description="Redirect to original URL"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="URL not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Not Found"),
     *             @OA\Property(property="message", type="string", example="URL not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Internal Server Error"),
     *             @OA\Property(property="message", type="string", example="An unexpected error occurred")
     *         )
     *     )
     * )
     */
    public function show(string $alias): RedirectResponse|JsonResponse
    {
        try {
            $url = $this->urlService->show($alias);

            return redirect()->away($url->original_url);
        } catch (UrlNotFoundException $e) {
            Log::warning('URL not found', [
                'alias' => $alias,
                'message' => $e->getMessage()
            ]);

            return $e->render();
        } catch (InternalServerErrorException $e) {
            Log::error('Failed to retrieve URL', [
                'alias' => $alias,
                'message' => $e->getMessage()
            ]);

            return $e->render();
        } catch (Exception $e) {
            Log::error('Unexpected error while retrieving URL', [
                'alias' => $alias,
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Internal Server Error',
                'message' => 'An unexpected error occurred'
            ], 500);
        }
    }
}
