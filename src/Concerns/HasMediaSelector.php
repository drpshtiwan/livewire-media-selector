<?php

namespace DrPshtiwan\LivewireMediaSelector\Concerns;

use DrPshtiwan\LivewireMediaSelector\Models\Media;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\DB;

trait HasMediaSelector
{
    public function media(): MorphToMany
    {
        return $this->morphToMany(
            Media::class,
            'mediable',
            config('media-selector.mediables_table', 'media_selector_mediables')
        )->withPivot(['collection', 'order_column'])->withTimestamps()->orderByPivot('order_column');
    }

    public function getMedia(?string $collection = null)
    {
        $rel = $this->media();

        return $collection ? $rel->wherePivot('collection', $collection)->get() : $rel->get();
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
