<?php

namespace DrPshtiwan\LivewireMediaSelector\Livewire;

use DrPshtiwan\LivewireMediaSelector\Livewire\Concerns\HandlesModal;
use DrPshtiwan\LivewireMediaSelector\Livewire\Concerns\HandlesSelection;
use DrPshtiwan\LivewireMediaSelector\Livewire\Concerns\HandlesTrash;
use DrPshtiwan\LivewireMediaSelector\Livewire\Concerns\HandlesUploads;
use DrPshtiwan\LivewireMediaSelector\Livewire\Concerns\InteractsWithValue;
use DrPshtiwan\LivewireMediaSelector\Livewire\Concerns\ManagesFilters;
use DrPshtiwan\LivewireMediaSelector\Livewire\Concerns\QueriesMedia;
use DrPshtiwan\LivewireMediaSelector\Models\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Modelable;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class MediaSelector extends Component
{
    use HandlesModal;
    use HandlesSelection;
    use HandlesTrash;
    use HandlesUploads;
    use InteractsWithValue;
    use ManagesFilters;
    use QueriesMedia;
    use WithFileUploads;
    use WithPagination;

    #[Modelable]
    public array|string|null $value = null;

    public bool $showModal = false;

    public string $search = '';

    public string $sort = 'newest';

    public bool $selectedOnly = false;

    public string $activeTab = 'browse'; // browse|upload

    // Security-sensitive configuration and permission flags below are marked
    // #[Locked]. They are resolved server-side in mount() from component
    // attributes and config, and must never be mutated from the browser.
    // Without locking, a crafted Livewire request could flip permissions
    // (e.g. canDelete/canUpload), widen the allowed file types to bypass
    // upload validation, or repoint the storage disk/directory.
    #[Locked]
    public string $disk;

    #[Locked]
    public string $directory;

    #[Locked]
    public int $perPage;

    #[Locked]
    public int $maxUploadKb;

    #[Locked]
    public array $allowedExtensions = [];

    #[Locked]
    public array $allowedMimes = [];

    #[Locked]
    public bool $multiple = false;

    protected string $mediaClass = Media::class;

    #[Locked]
    public bool $canDelete = false;

    #[Locked]
    public bool $canUpload = true;

    #[Locked]
    public bool $restrictToCurrentUser = false;

    // Optional image constraints for uploads
    #[Locked]
    public ?int $requireWidth = null;

    #[Locked]
    public ?int $requireHeight = null;

    #[Locked]
    public ?string $requireAspectRatio = null; // e.g. "16:9"

    #[Locked]
    public string $accept = '';

    protected bool $hasProvidedMimes = false;

    // Trash tab visibility and restore permission (attribute-driven)
    #[Locked]
    public bool $canSeeTrash = false;

    #[Locked]
    public bool $canRestoreTrash = false;

    #[Locked]
    public string $ui = 'tailwind';

    #[Locked]
    public bool $showThumbnails = true;

    // These receive attributes from the Blade tag: :mimes="[...]" :extensions="[...]"
    #[Locked]
    public array|string $mimes = [];

    #[Locked]
    public array|string $extensions = [];

    #[Locked]
    public ?string $collection = null;

    public array $uploads = [];

    public array $selectedIds = [];

    public function mount($value = null, ?bool $multiple = null, ?bool $canDelete = null, $extensions = null, $mimes = null, ?bool $canSeeTrash = null, ?bool $canRestoreTrash = null, ?string $ui = null, ?bool $canUpload = null, ?bool $restrictToCurrentUser = null, ?int $requireWidth = null, ?int $requireHeight = null, ?string $requireAspectRatio = null, ?string $collection = null, ?string $disk = null, ?string $directory = null, ?int $perPage = null, ?int $maxUploadKb = null, ?bool $showThumbnails = null): void
    {
        // Do not clobber model-bound value when using wire:model without passing :value explicitly
        if (is_array($value) || (is_string($value) && $value !== '') || is_int($value) || is_bool($value)) {
            $this->value = $value;
        }
        $resolvedDisk = trim((string) ($disk ?? ($this->disk ?? Config::get('media-selector.disk', 'public'))));
        $resolvedDirectory = trim((string) ($directory ?? ($this->directory ?? Config::get('media-selector.directory', 'media'))), '/');
        $resolvedPerPage = (int) ($perPage ?? ($this->perPage ?? Config::get('media-selector.per_page', 24)));
        $resolvedMaxUploadKb = (int) ($maxUploadKb ?? ($this->maxUploadKb ?? Config::get('media-selector.max_upload_kb', 5120)));

        $this->disk = $resolvedDisk !== '' ? $resolvedDisk : (string) Config::get('media-selector.disk', 'public');
        $this->directory = $resolvedDirectory !== '' ? $resolvedDirectory : 'media';
        $this->perPage = $resolvedPerPage > 0 ? $resolvedPerPage : 24;
        $this->maxUploadKb = $resolvedMaxUploadKb > 0 ? $resolvedMaxUploadKb : 5120;

        // Helper to normalize input into an array of strings
        $normalize = function ($raw): array {
            if (is_array($raw)) {
                return array_values(array_filter(array_map('trim', $raw), fn ($v) => is_string($v) && $v !== ''));
            }
            if (is_string($raw)) {
                $str = trim($raw);
                if ($str === '') {
                    return [];
                }
                // Try JSON decode if looks like array
                if ((str_starts_with($str, '[') && str_ends_with($str, ']')) || (str_starts_with($str, '"[') && str_ends_with($str, ']"'))) {
                    $decoded = json_decode($str, true);
                    if (is_array($decoded)) {
                        return array_values(array_filter(array_map('trim', $decoded), fn ($v) => is_string($v) && $v !== ''));
                    }
                }

                // CSV fallback
                return array_values(array_filter(array_map('trim', explode(',', $str)), fn ($v) => is_string($v) && $v !== ''));
            }

            return [];
        };

        // Prefer explicit mount params, then public properties (attributes), then config
        $providedMimes = $normalize($mimes ?? $this->mimes ?? []);
        $providedExts = $normalize($extensions ?? $this->extensions ?? []);

        $this->allowedExtensions = array_values(array_unique($providedExts ?: (array) Config::get('media-selector.allowed_extensions', ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])));
        $this->allowedMimes = array_values(array_unique($providedMimes ?: (array) Config::get('media-selector.allowed_mimes', [])));
        $this->hasProvidedMimes = count($providedMimes) > 0;

        $this->multiple = (bool) ($multiple ?? Config::get('media-selector.multiple', false));
        $this->mediaClass = (string) Config::get('media-selector.model', Media::class);
        $this->canDelete = (bool) ($canDelete ?? Config::get('media-selector.can_delete', false));
        $this->canUpload = (bool) ($canUpload ?? Config::get('media-selector.can_upload', true));
        $this->canSeeTrash = (bool) ($canSeeTrash ?? Config::get('media-selector.can_see_trash', false));
        $this->canRestoreTrash = (bool) ($canRestoreTrash ?? Config::get('media-selector.can_restore_trash', false));
        $requestedUi = strtolower((string) ($ui ?? Config::get('media-selector.ui', 'tailwind')));
        $this->ui = in_array($requestedUi, ['tailwind'], true) ? $requestedUi : 'tailwind';
        $this->showThumbnails = (bool) ($showThumbnails ?? Config::get('media-selector.show_thumbnails', true));
        $this->restrictToCurrentUser = (bool) ($restrictToCurrentUser ?? Config::get('media-selector.restrict_to_current_user', false));
        $this->requireWidth = $requireWidth;
        $this->requireHeight = $requireHeight;
        $this->requireAspectRatio = $requireAspectRatio ? trim($requireAspectRatio) : null;
        $this->collection = $collection ?? ($this->collection ?? null);

        // Build accept attribute. If mimes provided, accept only those; else include defaults + extensions
        $acceptParts = [];
        if ($this->hasProvidedMimes && count($this->allowedMimes)) {
            foreach ($this->allowedMimes as $mt) {
                if (! empty($mt)) {
                    $acceptParts[] = $mt;
                }
            }
        } else {
            foreach ($this->allowedMimes as $mt) {
                if (! empty($mt)) {
                    $acceptParts[] = $mt;
                }
            }
            foreach ($this->allowedExtensions as $ext) {
                if (! empty($ext)) {
                    $acceptParts[] = '.'.ltrim($ext, '.');
                }
            }
        }
        $this->accept = implode(',', array_values(array_unique($acceptParts)));

        // Normalize initial value paths for preview
        $this->normalizeValueForPreview($normalize);
        // Derive selection from value only outside the modal to avoid overriding user toggles
        if (! $this->showModal) {
            $this->setSelectedIdsFromValue();
        }
    }

    public function hydrate(): void
    {
        // Ensure that if the parent populates the bound value post-mount, preview still renders
        $normalize = function ($raw): array {
            if (is_array($raw)) {
                return array_values(array_filter(array_map('trim', $raw), fn ($v) => is_string($v) && $v !== ''));
            }
            if (is_string($raw)) {
                $str = trim($raw);
                if ($str === '') {
                    return [];
                }
                if ((str_starts_with($str, '[') && str_ends_with($str, ']')) || (str_starts_with($str, '"[') && str_ends_with($str, ']"'))) {
                    $decoded = json_decode($str, true);
                    if (is_array($decoded)) {
                        return array_values(array_filter(array_map('trim', $decoded), fn ($v) => is_string($v) && $v !== ''));
                    }
                }

                return array_values(array_filter(array_map('trim', explode(',', $str)), fn ($v) => is_string($v) && $v !== ''));
            }

            return [];
        };
        $this->normalizeValueForPreview($normalize);
        // Do not override user selection while the modal is open
        if (! $this->showModal) {
            $this->setSelectedIdsFromValue();
        }

        // If attributes were populated after mount, sync restrictions/accept dynamically
        $incomingMimes = $normalize($this->mimes ?? []);
        if (count($incomingMimes) > 0) {
            if (! $this->hasProvidedMimes || $incomingMimes !== $this->allowedMimes) {
                $this->allowedMimes = array_values(array_unique($incomingMimes));
                $this->hasProvidedMimes = true;

                // When explicit mimes provided, build accept strictly from them
                $acceptParts = [];
                foreach ($this->allowedMimes as $mt) {
                    if (! empty($mt)) {
                        $acceptParts[] = $mt;
                    }
                }
                $this->accept = implode(',', array_values(array_unique($acceptParts)));
            }
        }

        $incomingExts = $normalize($this->extensions ?? []);
        if (count($incomingExts) > 0) {
            $normalizedExts = array_values(array_unique($incomingExts));
            if ($normalizedExts !== $this->allowedExtensions) {
                $this->allowedExtensions = $normalizedExts;

                // Only impacts accept when mimes are not explicitly restricting
                if (! $this->hasProvidedMimes) {
                    $acceptParts = [];
                    foreach ($this->allowedMimes as $mt) {
                        if (! empty($mt)) {
                            $acceptParts[] = $mt;
                        }
                    }
                    foreach ($this->allowedExtensions as $ext) {
                        if (! empty($ext)) {
                            $acceptParts[] = '.'.ltrim($ext, '.');
                        }
                    }
                    $this->accept = implode(',', array_values(array_unique($acceptParts)));
                }
            }
        }
    }

    public function render()
    {
        return View::make('media-selector::livewire.media-selector', [
            'media' => $this->media,
            // Expose allowed types for Blade display
            'allowedTokens' => $this->buildAllowedTokensForDisplay(),
            'isMimeRestricted' => ($this->hasProvidedMimes && count($this->allowedMimes) > 0),
            'ui' => $this->ui,
        ]);
    }

    protected function scopedMediaQuery(bool $onlyTrashed = false, bool $withTrashed = false): Builder
    {
        $mediaClass = $this->mediaClass;
        $query = $mediaClass::query()->where('disk', $this->disk);

        if ($onlyTrashed) {
            $query->onlyTrashed();
        } elseif ($withTrashed) {
            $query->withTrashed();
        }

        if ($this->restrictToCurrentUser) {
            $query->where('user_id', Auth::id());
        }

        if (is_string($this->collection) && $this->collection !== '') {
            $query->where('collection', $this->collection);
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

        return $query;
    }

    public function safeMimeType(?string $path): ?string
    {
        if (! is_string($path) || $path === '') {
            return null;
        }

        try {
            $disk = Storage::disk($this->disk);

            return method_exists($disk, 'mimeType') ? ($disk->mimeType($path) ?: null) : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function safeUrl(?string $path): string
    {
        if (! is_string($path) || $path === '') {
            return '';
        }

        try {
            $disk = Storage::disk($this->disk);

            return (string) $disk->url($path);
        } catch (\Throwable $e) {
            return '';
        }
    }

    // Use Livewire's built-in WithPagination::previousPage/nextPage methods

}
