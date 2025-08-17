<?php

namespace Tests\Unit;

use App\Application\DTOs\Url\ExistsAliasDTO;
use App\Application\DTOs\Url\SaveUrlDTO;
use App\Application\DTOs\Url\ShowUrlDTO;
use App\Application\Services\UrlService;
use App\Domain\Url\Entities\UrlEntity;
use App\Domain\Url\Repositories\UrlRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Mockery;
use PHPUnit\Framework\TestCase;

class UrlServiceTest extends TestCase
{
    use RefreshDatabase;
    private UrlRepositoryInterface $urlRepository;
    private UrlService $urlService;

    protected function setUp(): void
    {
        parent::setUp();

        // Мокаем UrlRepository
        $this->urlRepository = Mockery::mock(UrlRepositoryInterface::class);
        $this->urlService = new UrlService($this->urlRepository);

    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testSave_SuccessfulSave_returnsUrlEntity()
    {
        $dto = new SaveUrlDTO(
            originalUrl: 'https://example.com',
            alias: Str::random(4)
        );

        $urlEntity = new UrlEntity(
            id: 1,
            originalUrl: $dto->originalUrl,
            alias: $dto->alias,
            createdAt: now()
        );


        $this->urlRepository->shouldReceive('aliasExists')
            ->withArgs(fn(ExistsAliasDTO $existsAliasDTO) => $existsAliasDTO->alias !== $dto->alias)
            ->andReturnFalse();

        $this->urlRepository
            ->shouldReceive('save')
            ->once()
            ->with(Mockery::on(function (SaveUrlDTO $arg) use ($dto) {
                return $arg->originalUrl === $dto->originalUrl
                    && $arg->alias === $dto->alias;
            }))
            ->andReturn($urlEntity);

        $result = $this->urlService->save($dto);

        $this->assertInstanceOf(UrlEntity::class, $result);
        $this->assertEquals($urlEntity->getId(), $result->getId());
        $this->assertEquals($urlEntity->getOriginalUrl(), $result->getOriginalUrl());
        $this->assertEquals($urlEntity->getAlias(), $result->getAlias());
    }

    public function testShow_SuccessfulShow_returnsUrlEntity()
    {
        $dto = new ShowUrlDTO(
            alias: 'testAlias',
            ip: '127.0.0.1'
        );

        $urlEntity = new UrlEntity(
            id: 1,
            originalUrl: 'https://example.com',
            alias: $dto->alias,
            createdAt: now()
        );

        $this->urlRepository
            ->shouldReceive('show')
            ->once()
            ->with(Mockery::on(function (ShowUrlDTO $arg) use ($dto) {
                return $arg->alias === $dto->alias
                    && $arg->ip === $dto->ip;
            }))
            ->andReturn($urlEntity);

        $result = $this->urlService->show($dto);

        $this->assertEquals($urlEntity->getId(), $result->getId());
        $this->assertEquals($urlEntity->getOriginalUrl(), $result->getOriginalUrl());
        $this->assertEquals($urlEntity->getAlias(), $result->getAlias());
    }
}