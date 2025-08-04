<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\Services\UrlServiceContract;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUrlRequest;
use App\Http\Resources\UrlResource;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *     version="1",
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
    use ResponseTrait;

    public function __construct(
        private readonly UrlServiceContract $urlService
    )
    {
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
     *     @OA\ResponseTrait(
     *         response=200,
     *         description="URL shortened successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="original_url", type="string", example="https://example.com"),
     *             @OA\Property(property="short_url", type="string", example="http://localhost/abc123")
     *         )
     *     ),
     *     @OA\ResponseTrait(
     *         response=422,
     *         description="Invalid URL format",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Invalid URL format"),
     *             @OA\Property(property="message", type="string", example="The URL format is invalid")
     *         )
     *     ),
     *     @OA\ResponseTrait(
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
        $urlData = $this->urlService->store(
            $request->input('original_url'),
        );

        return $this->success(
            data: new UrlResource($urlData),
            statusCode: 201
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v1/{alias}",
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
     *     @OA\ResponseTrait(
     *         response=302,
     *         description="Redirect to original URL"
     *     ),
     *     @OA\ResponseTrait(
     *         response=404,
     *         description="URL not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Not Found"),
     *             @OA\Property(property="message", type="string", example="URL not found")
     *         )
     *     ),
     *     @OA\ResponseTrait(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Internal Server Error"),
     *             @OA\Property(property="message", type="string", example="An unexpected error occurred")
     *         )
     *     )
     * )
     */
    public function show(Request $request, string $alias): JsonResponse
    {
        $url = $this->urlService->show(
            $alias,
            $request->ip(),
        );


        return $this->success(
            data: [
                'redirect_url' => $url->original_url,
            ],
        );
    }
}
