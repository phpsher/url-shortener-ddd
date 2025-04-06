<?php

namespace Tests\Feature;

use App\Models\Url;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UrlTest extends TestCase
{
    public function testStoreUrlSuccessfully()
    {
        $response = $this->post('/api/v1/store', [
            'original_url' => 'https://youtube.com',
        ]);



        $response->assertStatus(200);

        $this->assertDatabaseHas('urls', [
            'original_url' => 'https://youtube.com',
        ]);

        $this->assertNotNull($response['data']['short_url']);
        $this->assertStringStartsWith(env('APP_URL'), $response['data']['short_url']);
    }

    public function testShowUrlRedirectsToOriginalUrl()
    {
        $url = Url::factory()->create();

        $response = $this->get('/' . $url->alias);

        $response->assertRedirect($url->original_url);
    }
}
