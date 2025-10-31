<?php

namespace DrPshtiwan\LivewireMediaSelector\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use SoftDeletes;

    public const TABLE = 'media_selector_media';

    protected $table = self::TABLE;

    protected $fillable = [
        'user_id',
        'disk',
        'path',
        'filename',
        'collection',
        'mime',
        'size',
        'width',
        'height',
        'metadata',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'metadata' => 'array',
    ];

    public function getUrlAttribute(): string
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk($this->disk);

        return $disk->url($this->path);
    }

    public function getExtensionAttribute(): ?string
    {
        $name = (string) ($this->filename ?? '');
        if ($name === '') {
            return null;
        }
        $ext = pathinfo($name, PATHINFO_EXTENSION);

        return $ext ? strtolower((string) $ext) : null;
    }

    public function user(): BelongsTo
    {
        $userModel = Config::get('auth.providers.users.model');

        return $this->belongsTo($userModel ?? 'App\\Models\\User', 'user_id');
    }

    public function isPreviewable(): bool
    {
        $mime = (string) ($this->mime ?? '');
        if ($mime === '') {
            return false;
        }
        $allowed = (array) Config::get('media-selector.preview_mimes', ['image/*']);
        foreach ($allowed as $pattern) {
            $pattern = (string) $pattern;
            if (str_ends_with($pattern, '/*')) {
                $prefix = rtrim($pattern, '/*');
                if (str_starts_with($mime, $prefix.'/')) {
                    return true;
                }
            } else {
                if (strcasecmp($mime, $pattern) === 0) {
                    return true;
                }
            }
        }

        return false;
    }

    public function scopeForDisk($query, string $disk)
    {
        return $query->where('disk', $disk);
    }

    public function scopeForCollection($query, ?string $collection)
    {
        return $collection === null ? $query : $query->where('collection', $collection);
    }

    public function scopeSearch($query, string $term)
    {
        $like = '%'.$term.'%';

        return $query->where(function ($q) use ($like) {
            $q->where('filename', 'like', $like)
                ->orWhere('path', 'like', $like)
                ->orWhere('mime', 'like', $like)
                ->orWhere('metadata->original', 'like', $like);
        });
    }

    public function scopePreviewable($query)
    {
        $allowed = (array) Config::get('media-selector.preview_mimes', ['image/*']);

        return $query->where(function ($q) use ($allowed) {
            foreach ($allowed as $pattern) {
                $pattern = (string) $pattern;
                if (str_ends_with($pattern, '/*')) {
                    $prefix = rtrim($pattern, '/*');
                    $q->orWhere('mime', 'like', $prefix.'/%');
                } else {
                    $q->orWhere('mime', $pattern);
                }
            }
        });
    }
}
