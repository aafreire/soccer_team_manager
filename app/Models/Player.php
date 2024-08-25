<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    protected $fillable = [
        'name', 'level', 'is_goalkeeper', 'is_present', 'is_deleted',
    ];
}
