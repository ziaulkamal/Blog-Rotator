<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeywordData extends Model
{
    protected $table = 'keywords_data';

    protected $fillable = ['keyword', 'hit'];

    protected $casts = [
        'hit' => 'integer',
    ];
}
