<?php

namespace DrPshtiwan\LivewireMediaSelector\Livewire\Concerns;

use Illuminate\Pagination\LengthAwarePaginator;

trait QueriesMedia
{
    public function getMediaProperty(): LengthAwarePaginator
    {
        if (! $this->showModal) {
            return new LengthAwarePaginator(collect(), 0, $this->perPage, 1, [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'pageName' => 'lmsPage',
            ]);
        }

        $query = $this->scopedMediaQuery(
            onlyTrashed: $this->activeTab === 'trash' && $this->canSeeTrash
        );

        if ($this->search !== '') {
            $term = '%'.$this->search.'%';
            $query->where(function ($q) use ($term) {
                $q->where('filename', 'like', $term)
                    ->orWhere('path', 'like', $term)
                    ->orWhere('mime', 'like', $term)
                    ->orWhere('metadata->original', 'like', $term);
            });
        }

        if ($this->hasProvidedMimes && count($this->allowedMimes)) {
            $mimes = $this->allowedMimes;
            $query->where(function ($q) use ($mimes) {
                foreach ($mimes as $mt) {
                    if (str_ends_with($mt, '/*')) {
                        $prefix = rtrim($mt, '/*');
                        $q->orWhere('mime', 'like', $prefix.'/%');
                    } else {
                        $q->orWhere('mime', $mt);
                    }
                }
            });
        }

        if ($this->selectedOnly && count($this->selectedIds) > 0) {
            $query->whereIn('id', $this->selectedIds);
        }

        if ($this->sort === 'name_asc') {
            $query->orderBy('filename');
        } elseif ($this->sort === 'name_desc') {
            $query->orderByDesc('filename');
        } elseif ($this->sort === 'oldest') {
            $query->orderBy('id');
        } else {
            $query->orderByDesc('id');
        }

        return $query->paginate($this->perPage, ['*'], 'lmsPage');
    }
}
