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
            $this->resetErrorBag('selection');

            return;
        }

        $media = $this->scopedMediaQuery(
            onlyTrashed: $this->activeTab === 'trash' && $this->canSeeTrash
        )->find($id);
        if (! $media) {
            $this->addError('selection', 'The selected media item could not be found.');

            return;
        }

        $constraintError = $this->validateMediaSelectionConstraints($media);
        if ($constraintError !== null) {
            $this->addError('selection', $constraintError);

            return;
        }

        $this->resetErrorBag('selection');

        if ($this->multiple) {
            // add and reindex
            $this->selectedIds = array_values([...$ids, $id]);

            return;
        }

        // single select
        $this->selectedIds = [$id];
        $this->resetErrorBag('selection');
    }

    public function clearSelection(): void
    {
        $this->selectedIds = [];
        $this->resetErrorBag('selection');
    }

    public function clearPreview(): void
    {
        $this->value = $this->multiple ? [] : null;
        $this->selectedIds = [];
        $this->resetErrorBag('selection');
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

        if ($this->activeTab === 'trash') {
            $this->addError('selection', 'Cannot insert media from trash.');

            return;
        }

        $this->selectedIds = array_values(array_unique(array_map(fn ($v) => (int) $v, $this->selectedIds)));

        $records = $this->scopedMediaQuery()->whereIn('id', $this->selectedIds)->get();

        if ($records->count() !== count($this->selectedIds)) {
            $this->addError('selection', 'One or more selected media items are unavailable.');

            return;
        }

        foreach ($records as $record) {
            $constraintError = $this->validateMediaSelectionConstraints($record);
            if ($constraintError !== null) {
                $this->addError('selection', $constraintError);

                return;
            }
        }

        $this->resetErrorBag('selection');

        $items = $records
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

    protected function validateMediaSelectionConstraints($media): ?string
    {
        if (! ($this->requireWidth || $this->requireHeight || $this->requireAspectRatio)) {
            return null;
        }

        $width = isset($media->width) ? (int) $media->width : null;
        $height = isset($media->height) ? (int) $media->height : null;

        if ($this->requireWidth !== null) {
            if ($width === null) {
                return 'Selected image is missing width metadata and cannot be validated.';
            }

            if ($width !== (int) $this->requireWidth) {
                return 'Image width must be exactly '.(int) $this->requireWidth.'px.';
            }
        }

        if ($this->requireHeight !== null) {
            if ($height === null) {
                return 'Selected image is missing height metadata and cannot be validated.';
            }

            if ($height !== (int) $this->requireHeight) {
                return 'Image height must be exactly '.(int) $this->requireHeight.'px.';
            }
        }

        if ($this->requireAspectRatio && str_contains($this->requireAspectRatio, ':')) {
            if ($width === null || $height === null || $height === 0) {
                return 'Selected image is missing dimension metadata required to validate aspect ratio.';
            }

            [$ax, $ay] = array_pad(array_map('trim', explode(':', $this->requireAspectRatio, 2)), 2, null);
            $ax = (float) $ax;
            $ay = (float) $ay;
            if ($ax > 0.0 && $ay > 0.0) {
                $target = $ax / $ay;
                $actual = $width / $height;
                if (abs($actual - $target) > 0.01) {
                    return 'Image aspect ratio must be approximately '.$this->requireAspectRatio.'.';
                }
            }
        }

        return null;
    }
}
