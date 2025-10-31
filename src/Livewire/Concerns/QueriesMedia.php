<?php

namespace DrPshtiwan\LivewireMediaSelector\Livewire\Concerns;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

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

        $mediaClass = $this->mediaClass;

        $query = $mediaClass::query()->where('disk', $this->disk);
        if ($this->activeTab === 'trash' && $this->canSeeTrash) {
            $query->onlyTrashed();
        }

        if ($this->restrictToCurrentUser) {
            $query->where('user_id', Auth::id());
        }

        $extraScope = Config::get('media-selector.extra_scope');
        if (is_string($extraScope) && str_contains($extraScope, '@')) {
            [$class, $method] = explode('@', $extraScope, 2);
            if (class_exists($class) && method_exists($class, $method)) {
                (new $class)->{$method}($query, $this);
            }
        } elseif (is_callable($extraScope)) {
            $extraScope($query, $this);
        }

        if (is_string($this->collection) && $this->collection !== '') {
            $query->where('collection', $this->collection);
        }

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
