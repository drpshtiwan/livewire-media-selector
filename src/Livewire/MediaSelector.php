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
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;
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

    public string $disk;

    public string $directory;

    public int $perPage;

    public int $maxUploadKb;

    public array $allowedExtensions = [];

    public array $allowedMimes = [];

    public bool $multiple = false;

    protected string $mediaClass = \DrPshtiwan\LivewireMediaSelector\Models\Media::class;

    public bool $canDelete = false;

    public bool $canUpload = true;

    public bool $restrictToCurrentUser = false;

    // Optional image constraints for uploads
    public ?int $requireWidth = null;

    public ?int $requireHeight = null;

    public ?string $requireAspectRatio = null; // e.g. "16:9"

    public string $accept = '';

    protected bool $hasProvidedMimes = false;

    // Trash tab visibility and restore permission (attribute-driven)
    public bool $canSeeTrash = false;

    public bool $canRestoreTrash = false;

    public string $ui = 'tailwind';

    // These receive attributes from the Blade tag: :mimes="[...]" :extensions="[...]"
    public array|string $mimes = [];

    public array|string $extensions = [];

    public ?string $collection = null;

    public array $uploads = [];

    public array $selectedIds = [];

    public function mount($value = null, ?bool $multiple = null, ?bool $canDelete = null, $extensions = null, $mimes = null, ?bool $canSeeTrash = null, ?bool $canRestoreTrash = null, ?string $ui = null, ?bool $canUpload = null, ?bool $restrictToCurrentUser = null, ?int $requireWidth = null, ?int $requireHeight = null, ?string $requireAspectRatio = null, ?string $collection = null): void
    {
        // Do not clobber model-bound value when using wire:model without passing :value explicitly
        if (is_array($value) || (is_string($value) && $value !== '') || is_int($value) || is_bool($value)) {
            $this->value = $value;
        }
        $this->disk = (string) Config::get('media-selector.disk', 'public');
        $this->directory = trim((string) Config::get('media-selector.directory', 'media'), '/');
        $this->perPage = (int) Config::get('media-selector.per_page', 24);
        $this->maxUploadKb = (int) Config::get('media-selector.max_upload_kb', 5120);

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
        $this->ui = (string) ($ui ?? Config::get('media-selector.ui', 'tailwind'));
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

    // Use Livewire's built-in WithPagination::previousPage/nextPage methods

}
