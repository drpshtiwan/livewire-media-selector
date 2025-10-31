<?php

namespace DrPshtiwan\LivewireMediaSelector\Livewire\Concerns;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

trait HandlesUploads
{
    public function clearUploads(): void
    {
        $this->uploads = [];
    }

    public function removeUploadAt(int $index): void
    {
        if (! is_array($this->uploads)) {
            return;
        }
        if ($index < 0 || $index >= count($this->uploads)) {
            return;
        }
        unset($this->uploads[$index]);
        $this->uploads = array_values($this->uploads);
    }

    public function saveUpload(): void
    {
        if (! $this->canUpload) {
            return;
        }
        $rulesForEach = ['file'];
        if ($this->hasProvidedMimes && count($this->allowedMimes)) {
            $rulesForEach[] = 'mimetypes:'.implode(',', $this->allowedMimes);
        } elseif (count($this->allowedExtensions)) {
            $extensions = array_filter($this->allowedExtensions, fn ($ext) => $ext !== 'svg');
            if (count($extensions)) {
                $rulesForEach[] = 'mimes:'.implode(',', $extensions);
            }
        }
        $rulesForEach[] = 'max:'.$this->maxUploadKb;

        $this->validate([
            'uploads' => 'required|array|min:1',
            'uploads.*' => implode('|', $rulesForEach),
        ]);

        if (($this->requireWidth || $this->requireHeight || $this->requireAspectRatio)) {
            foreach ((array) $this->uploads as $file) {
                $mimeGuess = method_exists($file, 'getClientMimeType') ? (string) $file->getClientMimeType() : '';
                if (! $mimeGuess || ! str_starts_with($mimeGuess, 'image/')) {
                    $this->addError('uploads', 'Only image files are allowed when size/aspect constraints are set.');

                    return;
                }

                $dims = null;
                try {
                    $path = method_exists($file, 'getRealPath') ? $file->getRealPath() : null;
                    if ($path && is_file($path)) {
                        $dims = @getimagesize($path);
                    }
                } catch (\Throwable $e) {
                }

                if (! is_array($dims) || ! isset($dims[0], $dims[1])) {
                    $this->addError('uploads', 'Could not read image dimensions.');

                    return;
                }

                $w = (int) ($dims[0] ?? 0);
                $h = (int) ($dims[1] ?? 0);
                $reqW = $this->requireWidth !== null ? (int) $this->requireWidth : null;
                $reqH = $this->requireHeight !== null ? (int) $this->requireHeight : null;

                if ($reqW !== null && $w !== $reqW) {
                    $this->addError('uploads', 'Image width must be exactly '.$reqW.'px.');

                    return;
                }
                if ($reqH !== null && $h !== $reqH) {
                    $this->addError('uploads', 'Image height must be exactly '.$reqH.'px.');

                    return;
                }

                if ($this->requireAspectRatio && str_contains($this->requireAspectRatio, ':')) {
                    [$ax, $ay] = array_pad(array_map('trim', explode(':', $this->requireAspectRatio, 2)), 2, null);
                    $ax = (float) $ax;
                    $ay = (float) $ay;
                    if ($ax > 0 && $ay > 0) {
                        $target = $ax / $ay;
                        $actual = $h > 0 ? ($w / $h) : 0.0;
                        if (abs($actual - $target) > 0.01) {
                            $this->addError('uploads', 'Image aspect ratio must be approximately '.$this->requireAspectRatio.'.');

                            return;
                        }
                    }
                }
            }
        }

        $disk = Storage::disk($this->disk);
        $created = [];

        foreach ($this->uploads as $file) {
            $storedPath = $file->store($this->directory, $this->disk);

            $filename = basename($storedPath);
            $mime = method_exists($file, 'getClientMimeType') ? $file->getClientMimeType() : null;
            $size = $disk->size($storedPath);

            $width = null;
            $height = null;
            if (method_exists($disk, 'path')) {
                $absolutePath = $disk->path($storedPath);
                if (is_file($absolutePath) && function_exists('getimagesize')) {
                    $dims = @getimagesize($absolutePath);
                    if (is_array($dims)) {
                        $width = (int) ($dims[0] ?? null);
                        $height = (int) ($dims[1] ?? null);
                    }
                }
            }

            $media = ($this->mediaClass)::create([
                'user_id' => Auth::id(),
                'disk' => $this->disk,
                'path' => $storedPath,
                'filename' => $filename,
                'collection' => $this->collection,
                'mime' => $mime,
                'size' => $size,
                'width' => $width,
                'height' => $height,
                'metadata' => ['original' => method_exists($file, 'getClientOriginalName') ? $file->getClientOriginalName() : null],
            ]);

            $created[] = $media;
        }

        if (! empty($created)) {
            $newIds = array_map(fn ($m) => $m->id, $created);
            if ($this->multiple) {
                $this->selectedIds = array_values(array_unique(array_merge($this->selectedIds, $newIds)));
            } else {
                $this->selectedIds = [$newIds[0]];
            }
        }

        $this->reset('uploads');
        $this->resetPage('lmsPage');

        $items = \collect($created)->map(fn ($m) => [
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

        $this->dispatch('media-uploaded', items: $items);

        $this->activeTab = 'browse';
        $this->showModal = true;
    }
}
