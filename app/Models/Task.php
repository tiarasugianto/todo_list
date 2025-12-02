<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'name',
        'is_done',
        'priority',
        'date',
        'category',
        'order_index',
    ];
}
