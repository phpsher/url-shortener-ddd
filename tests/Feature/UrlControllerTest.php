<?php

namespace Tests\Feature;

use App\Infrastructure\Persistence\Url\Models\UrlModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Request;
use Tests\TestCase;

class UrlControllerTest extends TestCase
{
    use RefreshDatabase;

    private UrlModel $url;

    protected function setUp(): void
    {
        parent::setUp();
        $this->url = UrlModel::factory()->create();
    }

    public function testStoreUrlSuccessfully()
    {
        $this->post('/api/v1/urls', [
            'original_url' => 'https://youtube.com',
        ])
            ->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'original_url',
                    'short_url'
                ]
            ]);

        $this->assertDatabaseHas('urls', [
            'original_url' => 'https://youtube.com',
        ]);
    }

    public function testShowUrlSuccessfully()
    {
        $this->get('/api/v1/urls/' . $this->url->alias)
            ->assertJsonStructure([
                'data' => [
                    'redirect_url'
                ]
            ]);

    }

    public function testHasClickForUrl()
    {
        $this->get('/api/v1/urls/' . $this->url->alias)
            ->assertStatus(200);

        $this->assertDatabaseHas('clicks', [
            'url_id' => $this->url->id,
            'ip' => Request::ip()
        ]);
    }
}
