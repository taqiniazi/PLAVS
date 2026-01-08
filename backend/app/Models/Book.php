<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'isbn',
        'edition',
        'publisher',
        'publish_date',
        'shelf_location',
        'owner',
        'assigned_user_id',
        'description',
        'visibility',
        'status',
        'image',
        'cover_image'
    ];

    protected $casts = [
        'visibility' => 'boolean',
        'publish_date' => 'date'
    ];
}
