<?php

namespace App\Infrastructure\Persistence\Url\Models;

use Database\Factories\UrlModelFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[UseFactory(UrlModelFactory::class)]
class UrlModel extends Model
{
    use HasFactory;

    protected $table = 'urls';
    protected $fillable = [
        'original_url',
        'alias',
    ];
}
