<?php

namespace App\Infrastructure\Persistence\Click\Models;

use Illuminate\Database\Eloquent\Model;

class ClickModel extends Model
{
    protected $table = 'clicks';
    protected $fillable = [
        'url_id',
        'ip'
    ];
}
