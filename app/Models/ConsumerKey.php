<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsumerKey extends Model
{
    protected $table = 'secret_key';

    protected $fillable = [
        'accounts',
        'apiKey',
        'cseId'
    ];
}
