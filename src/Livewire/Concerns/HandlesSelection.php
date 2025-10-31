<?php

namespace DrPshtiwan\LivewireMediaSelector\Livewire\Concerns;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

trait HandlesSelection
{
    public function selectMedia(string $path): void
    {
        $this->value = $path;
        $this->closeModal();
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk($this->disk);
        $this->dispatch('media-selected', path: $path, url: $disk->url($path));
    }

    public function toggleSelect(int $id): void
    {
        $ids = $this->selectedIds ?? [];

        if (in_array($id, $ids, true)) {
            // remove and reindex
            $this->selectedIds = array_values(array_diff($ids, [$id]));

            return;
        }

        if ($this->multiple) {
            // add and reindex
            $this->selectedIds = array_values([...$ids, $id]);

            return;
        }

        // single select
        $this->selectedIds = [$id];

    }

    public function clearSelection(): void
    {
        $this->selectedIds = [];
    }

    public function clearPreview(): void
    {
        $this->value = $this->multiple ? [] : null;
        $this->selectedIds = [];
    }

    public function removeFromValue($identifier): void
    {
        if (is_array($this->value)) {
            $this->value = array_values(array_filter($this->value, function ($entry) use ($identifier) {
                if (is_array($entry)) {
                    $id = $entry['id'] ?? null;
                    $path = $entry['path'] ?? null;
                    if (is_numeric($identifier) && $id !== null) {
                        return (int) $id !== (int) $identifier;
                    }
                    if (is_string($identifier) && $path !== null) {
                        return $path !== $identifier;
                    }

                    return true;
                }
                if (is_string($entry) && is_string($identifier)) {
                    return $entry !== $identifier;
                }

                return true;
            }));
            if (is_numeric($identifier)) {
                $this->selectedIds = array_values(array_filter($this->selectedIds, fn ($id) => (int) $id !== (int) $identifier));
            } elseif (is_string($identifier)) {
                foreach ($this->value as $entry) {
                    if (is_array($entry) && ($entry['path'] ?? null) === $identifier && isset($entry['id'])) {
                        $this->selectedIds = array_values(array_filter($this->selectedIds, fn ($id) => (int) $id !== (int) $entry['id']));
                        break;
                    }
                }
            }
        } elseif (is_string($this->value) && is_string($identifier) && $this->value === $identifier) {
            $this->value = null;
        }
    }

    public function moveValueItem(int $fromIndex, int $toIndex): void
    {
        if (! is_array($this->value)) {
            return;
        }
        $count = count($this->value);
        if ($fromIndex < 0 || $toIndex < 0 || $fromIndex >= $count || $toIndex >= $count) {
            return;
        }
        if ($fromIndex === $toIndex) {
            return;
        }
        $items = array_values($this->value);
        $removed = array_splice($items, $fromIndex, 1);
        $element = $removed[0] ?? null;
        if ($element === null) {
            return;
        }
        if ($fromIndex < $toIndex) {
            $toIndex--;
        }
        array_splice($items, $toIndex, 0, [$element]);
        $this->value = array_values($items);
    }

    public function getSelectedItemsProperty(): Collection
    {
        if (count($this->selectedIds) === 0) {
            return \collect();
        }
        $mediaClass = $this->mediaClass;

        return $mediaClass::query()->whereIn('id', $this->selectedIds)->get();
    }

    public function confirmSelection(): void
    {
        if (count($this->selectedIds) === 0) {
            return;
        }

        $mediaClass = $this->mediaClass;
        $this->selectedIds = array_values(array_unique(array_map(fn ($v) => (int) $v, $this->selectedIds)));

        $items = $mediaClass::query()->whereIn('id', $this->selectedIds)->get()
            ->map(fn ($m) => [
                'id' => $m->id,
                'disk' => $m->disk,
                'path' => $m->path,
                'url' => $m->url,
                'filename' => $m->filename,
                'size' => $m->size,
                'mime' => $m->mime,
                'width' => $m->width,
                'height' => $m->height,
                'collection' => $m->collection,
                'original' => is_array($m->metadata) ? ($m->metadata['original'] ?? null) : (is_object($m->metadata) ? ($m->metadata->original ?? null) : null),
            ])->values()->all();

        $items = \collect($items)->unique(fn ($it) => $it['id'])->values()->all();

        $newPayload = array_map(fn ($it) => [
            'id' => $it['id'],
            'collection' => $it['collection'] ?? null,
            'path' => $it['path'],
        ], $items);

        if ($this->multiple || is_array($this->value)) {
            // Replace current value entirely with the selection
            $this->value = array_values($newPayload);
        } else {
            $this->value = $newPayload;
        }

        $this->dispatch('media-added', items: $items);
        $this->selectedIds = [];
        $this->closeModal();
    }
}
