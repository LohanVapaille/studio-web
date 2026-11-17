<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'source',
        'is_read',
    ];

    public const SOURCES = [
        'lovaweb' => 'Lovaweb',
        'portfolio' => 'Portfolio',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];
}