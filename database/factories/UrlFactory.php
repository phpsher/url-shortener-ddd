<?php

namespace Database\Factories;

use App\Models\Url;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Url>
 */
class UrlFactory extends Factory
{
    protected $table = 'urls';

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'original_url' => 'https://youtube.com',
            'alias' => Str::random(4),
        ];
    }
}
