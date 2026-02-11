<?php

namespace DrPshtiwan\LivewireMediaSelector\Concerns;

use DrPshtiwan\LivewireMediaSelector\Models\Media;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

trait HasMediaSelector
{
    public function media(): MorphToMany
    {
        $mediaModel = (string) config('media-selector.model', Media::class);

        return $this->morphToMany(
            $mediaModel,
            'mediable',
            config('media-selector.mediables_table', 'media_selector_mediables')
        )->withPivot(['collection', 'order_column'])->withTimestamps()->orderByPivot('order_column');
    }

    public function getMedia(?string $collection = null)
    {
        if ($this->relationLoaded('media')) {
            $items = $this->getRelation('media');

            return $collection === null
                ? $items
                : $items->filter(function ($media) use ($collection) {
                    return optional($media->pivot)->collection === $collection;
                })->values();
        }

        $rel = $this->media();

        return $collection ? $rel->wherePivot('collection', $collection)->get() : $rel->get();
    }

    /**
     * Scope a query to eager load the media relationship, optionally filtered by collection.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     */
    public function scopeWithMediaCollection($query, ?string $collection = null, ?callable $callback = null)
    {
        return $query->with([
            'media' => function ($relation) use ($collection, $callback) {
                if ($collection !== null) {
                    $relation->wherePivot('collection', $collection);
                }

                if (is_callable($callback)) {
                    $callback($relation);
                }
            },
        ]);
    }

    public function getMediaUrls(?string $collection = null): array
    {
        return $this->getMedia($collection)
            ->map(function ($media) {
                $disk = method_exists($media, 'getAttribute')
                    ? $media->getAttribute('disk')
                    : ($media->disk ?? null);
                $path = method_exists($media, 'getAttribute')
                    ? $media->getAttribute('path')
                    : ($media->path ?? null);

                if (! is_string($path) || $path === '') {
                    return null;
                }

                $diskName = is_string($disk) && $disk !== ''
                    ? $disk
                    : (string) config('media-selector.disk', config('filesystems.default', 'public'));

                try {
                    /** @var \Illuminate\Filesystem\FilesystemAdapter $adapter */
                    $adapter = Storage::disk($diskName);

                    return (string) $adapter->url($path);
                } catch (\Throwable $e) {
                    try {
                        $defaultDisk = (string) config('filesystems.default', 'public');
                        /** @var \Illuminate\Filesystem\FilesystemAdapter $adapter */
                        $adapter = Storage::disk($defaultDisk);

                        return (string) $adapter->url($path);
                    } catch (\Throwable $inner) {
                        return method_exists($media, 'getAttribute')
                            ? (string) $media->getAttribute('url')
                            : (string) ($media->url ?? '');
                    }
                }
            })
            ->filter(fn ($url) => is_string($url) && $url !== '')
            ->values()
            ->all();
    }

    public function getMediaUrl(?string $collection = null): ?string
    {
        $urls = $this->getMediaUrls($collection);

        return count($urls) ? $urls[0] : null;
    }

    public function getMediaPayload(?string $collection = null): array
    {
        $items = $this->getMedia($collection)->sortBy(function ($m) {
            return (int) (optional($m->pivot)->order_column ?? 0);
        })->values();

        return $items->map(function ($m) {
            return [
                'id' => $m->id,
                'collection' => optional($m->pivot)->collection,
                'path' => $m->path,
            ];
        })->values()->all();
    }

    public function attachMedia(?array $items, ?string $collection = null): void
    {
        $items = is_array($items) ? $items : [];
        $pivot = config('media-selector.mediables_table', 'media_selector_mediables');
        $now = now();
        $modelType = method_exists($this, 'getMorphClass') ? $this->getMorphClass() : static::class;
        $modelId = $this->getKey();

        $order = 1;
        foreach ($items as $it) {
            if (is_array($it)) {
                $mediaId = $it['id'] ?? null;
                $col = $it['collection'] ?? $collection;
            } else {
                $mediaId = $it; // assume numeric id
                $col = $collection;
            }
            if (! is_numeric($mediaId)) {
                continue;
            }
            DB::table($pivot)->updateOrInsert(
                [
                    'media_id' => (int) $mediaId,
                    'mediable_type' => $modelType,
                    'mediable_id' => $modelId,
                    'collection' => $col,
                ],
                [
                    'order_column' => $order++,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }

    public function syncMedia(?array $items, ?string $collection = null): void
    {
        $pivot = config('media-selector.mediables_table', 'media_selector_mediables');
        $modelType = method_exists($this, 'getMorphClass') ? $this->getMorphClass() : static::class;
        $modelId = $this->getKey();

        DB::transaction(function () use ($items, $collection, $pivot, $modelType, $modelId) {
            $source = is_array($items) ? $items : [];
            $ids = collect($source)->map(function ($it) {
                return is_array($it) ? ($it['id'] ?? null) : $it;
            })->filter(fn ($v) => is_numeric($v))->map(fn ($v) => (int) $v)->values();

            $query = DB::table($pivot)
                ->where('mediable_type', $modelType)
                ->where('mediable_id', $modelId);
            if ($collection !== null) {
                $query->where('collection', $collection);
            }
            if ($ids->count()) {
                $query->whereNotIn('media_id', $ids)->delete();
            } else {
                $query->delete();
            }

            $this->attachMedia($source, $collection);
        });
    }

    public function detachMedia($ids, ?string $collection = null): void
    {
        $pivot = config('media-selector.mediables_table', 'media_selector_mediables');
        $modelType = method_exists($this, 'getMorphClass') ? $this->getMorphClass() : static::class;
        $modelId = $this->getKey();

        $ids = collect(is_array($ids) ? $ids : [$ids])
            ->map(fn ($v) => is_array($v) ? ($v['id'] ?? null) : $v)
            ->filter(fn ($v) => is_numeric($v))
            ->map(fn ($v) => (int) $v)
            ->values();

        if (! $ids->count()) {
            return;
        }

        $query = DB::table($pivot)
            ->where('mediable_type', $modelType)
            ->where('mediable_id', $modelId)
            ->whereIn('media_id', $ids);
        if ($collection !== null) {
            $query->where('collection', $collection);
        }
        $query->delete();
    }
}
