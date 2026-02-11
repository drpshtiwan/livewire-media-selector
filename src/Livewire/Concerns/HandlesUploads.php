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
        $rulesForEach[] = 'max:'.$this->maxUploadKb;

        $this->validate([
            'uploads' => 'required|array|min:1',
            'uploads.*' => implode('|', $rulesForEach),
        ]);

        $mimeCache = [];
        foreach ((array) $this->uploads as $file) {
            $mimeGuess = (string) ($this->guessUploadMime($file) ?? '');
            $mimeCache[spl_object_id($file)] = $mimeGuess;

            if (count($this->allowedMimes)) {
                if ($mimeGuess === '' || ! $this->mimeMatchesAllowed($mimeGuess)) {
                    $this->addError('uploads', 'The uploaded file type is not allowed.');

                    return;
                }
            } elseif (count($this->allowedExtensions)) {
                $extension = null;
                if (method_exists($file, 'getClientOriginalExtension')) {
                    $extension = $file->getClientOriginalExtension();
                }
                if (! $extension && method_exists($file, 'getClientOriginalName')) {
                    $original = $file->getClientOriginalName();
                    $extension = $original ? pathinfo($original, PATHINFO_EXTENSION) : null;
                }
                $extension = is_string($extension) ? strtolower($extension) : null;

                $allowedExtensions = array_values(array_filter(array_map(
                    fn ($ext) => is_string($ext) ? strtolower($ext) : null,
                    $this->allowedExtensions
                )));

                if ($extension === null || ($allowedExtensions && ! in_array($extension, $allowedExtensions, true))) {
                    $this->addError('uploads', 'The uploaded file extension is not allowed.');

                    return;
                }
            }
        }

        if (($this->requireWidth || $this->requireHeight || $this->requireAspectRatio)) {
            foreach ((array) $this->uploads as $file) {
                $mimeGuess = (string) ($mimeCache[spl_object_id($file)] ?? $this->guessUploadMime($file) ?? '');
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
            $mime = $this->guessUploadMime($file, $disk, $storedPath);
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

    protected function guessUploadMime($file, $disk = null, ?string $storedPath = null): ?string
    {
        $candidates = [];

        if (method_exists($file, 'getMimeType')) {
            $candidates[] = $file->getMimeType();
        }
        if (method_exists($file, 'getClientMimeType')) {
            $candidates[] = $file->getClientMimeType();
        }

        $realPath = method_exists($file, 'getRealPath') ? $file->getRealPath() : null;
        if ($realPath && is_file($realPath) && function_exists('mime_content_type')) {
            $candidates[] = @mime_content_type($realPath);
        }

        if ($storedPath && $disk && method_exists($disk, 'mimeType')) {
            try {
                $candidates[] = $disk->mimeType($storedPath);
            } catch (\Throwable $e) {
            }
        }

        foreach ($candidates as $candidate) {
            if (is_string($candidate) && $candidate !== '' && strtolower((string) $candidate) !== 'application/octet-stream') {
                return $candidate;
            }
        }

        foreach ($candidates as $candidate) {
            if (is_string($candidate) && $candidate !== '') {
                return $candidate;
            }
        }

        return null;
    }

    protected function mimeMatchesAllowed(string $mime): bool
    {
        if (! count($this->allowedMimes)) {
            return true;
        }

        $mime = strtolower($mime);

        foreach ($this->allowedMimes as $pattern) {
            if (! is_string($pattern) || $pattern === '') {
                continue;
            }

            $pattern = strtolower($pattern);

            if ($pattern === '*') {
                return true;
            }

            if (str_ends_with($pattern, '/*')) {
                $prefix = substr($pattern, 0, -1);
                if (str_starts_with($mime, $prefix)) {
                    return true;
                }
            } elseif (strcasecmp($mime, $pattern) === 0) {
                return true;
            }
        }

        return false;
    }
}
