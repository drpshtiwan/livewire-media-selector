<?php

namespace DrPshtiwan\LivewireMediaSelector\Livewire\Concerns;

use Illuminate\Support\Facades\Storage;

trait HandlesTrash
{
    public function deleteMedia(int $id): void
    {
        if (! $this->canDelete) {
            return;
        }
        $media = $this->scopedMediaQuery()->find($id);
        if (! $media) {
            return;
        }

        $media->delete();
        $this->selectedIds = array_values(array_diff($this->selectedIds, [$id]));
        $this->dispatch('media-deleted', id: $id);
        $this->resetPage('lmsPage');
    }

    public function deleteSelected(): void
    {
        foreach ($this->selectedIds as $id) {
            $this->deleteMedia((int) $id);
        }
    }

    public function restoreMedia(int $id): void
    {
        if (! $this->canRestoreTrash) {
            return;
        }
        $media = $this->scopedMediaQuery(onlyTrashed: true)->find($id);
        if (! $media) {
            return;
        }
        $media->restore();
        $this->selectedIds = array_values(array_diff($this->selectedIds, [$id]));
        $this->dispatch('media-restored', id: $id);
        $this->resetPage('lmsPage');
    }

    public function restoreSelected(): void
    {
        if (! $this->canRestoreTrash) {
            return;
        }
        foreach ($this->selectedIds as $id) {
            $this->restoreMedia((int) $id);
        }
    }

    public function forceDeleteMedia(int $id): void
    {
        if (! $this->canDelete) {
            return;
        }
        $media = $this->scopedMediaQuery(onlyTrashed: true)->find($id);
        if (! $media) {
            return;
        }

        try {
            $disk = Storage::disk($this->disk);
            if ($media->path && method_exists($disk, 'exists')) {
                if ($disk->exists($media->path)) {
                    $disk->delete($media->path);
                }
            }
        } catch (\Throwable $e) {
        }

        $media->forceDelete();
        $this->selectedIds = array_values(array_diff($this->selectedIds, [$id]));
        $this->dispatch('media-deleted', id: $id);
        $this->resetPage('lmsPage');
    }

    public function forceDeleteSelected(): void
    {
        if (! $this->canDelete) {
            return;
        }
        foreach ($this->selectedIds as $id) {
            $this->forceDeleteMedia((int) $id);
        }
    }
}
