<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FileUpload extends Model
{
    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'original_name',
        'path',
        'disk',
        'mime_type',
        'extension',
        'size',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (FileUpload $file): void {
            if (!$file->id) {
                $file->id = (string) Str::uuid();
            }
        });
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('expires_at', '<=', now());
    }
}
