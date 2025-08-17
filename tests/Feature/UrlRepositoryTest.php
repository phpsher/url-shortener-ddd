<?php

namespace Tests\Feature;

use App\Application\DTOs\Click\ExistsClickDTO;
use App\Application\DTOs\Url\SaveUrlDTO;
use App\Application\DTOs\Url\ShowUrlDTO;
use App\Domain\Click\Repository\ClickRepositoryInterface;
use App\Domain\Url\Entities\UrlEntity;
use App\Domain\Url\Repositories\UrlRepositoryInterface;
use App\Infrastructure\Persistence\Url\Models\UrlModel;
use App\Infrastructure\Persistence\Url\Repository\UrlRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class UrlRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private UrlRepositoryInterface $urlRepository;
    private ClickRepositoryInterface $clickRepository;
    private UrlEntity $urlEntity;

    protected function setUp(): void
    {
        parent::setUp();

        // Мокаем ClickRepository чтобы создать UrlRepository
        $this->mockClickRepository = Mockery::mock(ClickRepositoryInterface::class);
        $this->urlRepository = new UrlRepository($this->mockClickRepository);

        $urlModel = UrlModel::factory()->create(); // Создаем модель

        // На основе модели создаем сущность
        $this->urlEntity = new UrlEntity(
            id: $urlModel->id,
            originalUrl: $urlModel->original_url,
            alias: $urlModel->alias,
            createdAt: $urlModel->created_at,
        );
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
            alias: Str::random(6)
        );

        $entity = $this->urlRepository->save($dto);

        $this->assertEquals($dto->originalUrl, $entity->getOriginalUrl());
        $this->assertEquals($dto->alias, $entity->getAlias());

        $this->assertDatabaseHas('urls', [
            'original_url' => $dto->originalUrl,
            'alias' => $dto->alias,
        ]);
    }

    public function testShow_SuccessfulReturnsUrlEntity()
    {
        $dto = new ShowUrlDTO(
            alias: $this->urlEntity->getAlias(),
            ip: request()->ip(),
        );

        $this->mockClickRepository
            ->shouldReceive('exists')
            ->once()
            ->with(Mockery::on(function (ExistsClickDTO $arg) use ($dto) {
                return $arg->url_id === $this->urlEntity->getId()
                    && $arg->ip === $dto->ip;
            }))
            ->andReturn(true);

        $entity = $this->urlRepository->show($dto);

        $this->assertEquals($this->urlEntity->getId(), $entity->getId());
        $this->assertEquals($this->urlEntity->getOriginalUrl(), $entity->getOriginalUrl());
    }

}
