<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class ToDo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'profile_id',
        'title',
        'description',
        'is_done'
    ];
}
