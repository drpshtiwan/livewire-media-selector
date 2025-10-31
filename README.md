# Livewire Media Selector

[![Packagist Version](https://img.shields.io/packagist/v/drpshtiwan/livewire-media-selector.svg?style=flat-square)](https://packagist.org/packages/drpshtiwan/livewire-media-selector)
[![Total Downloads](https://img.shields.io/packagist/dt/drpshtiwan/livewire-media-selector.svg?style=flat-square)](https://packagist.org/packages/drpshtiwan/livewire-media-selector)
[![License](https://img.shields.io/packagist/l/drpshtiwan/livewire-media-selector.svg?style=flat-square)](LICENSE)
[![Tests](https://img.shields.io/github/actions/workflow/status/drpshtiwan/livewire-media-selector/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/drpshtiwan/livewire-media-selector/actions)
[![Code Style: Laravel Pint](https://img.shields.io/badge/code%20style-Laravel%20Pint-FF2D20?style=flat-square)](https://github.com/laravel/pint)
[![PHP Version](https://img.shields.io/packagist/php-v/drpshtiwan/livewire-media-selector?style=flat-square)](composer.json)

A lightweight, WordPress-style media selector for Laravel applications powered by Livewire.


## Screenshots

![Media selector preview](assets/image.png)

### Video demo

[![Watch the demo on YouTube](https://img.youtube.com/vi/4Snjk2213ls/hqdefault.jpg)](https://www.youtube.com/watch?v=4Snjk2213ls)

### Features
- Browse media (images, audio, video, documents) from your configured disk/directory
- Search, paginate, and sort (newest/oldest, name A→Z/Z→A)
- Selected-only filter to focus on chosen items
- Upload files and select immediately (multi-file with progress)
- Multi-select with drag-to-reorder selected items
- Compact, responsive square thumbnails for consistent previews
- Copy URL button on every item (modal and preview) + toast feedback
- Delete with confirmation (per-item and bulk, when enabled)
- Trash tab (soft deletes) with per-item and bulk restore
- Configurable filters via `allowed_mimes` or `allowed_extensions`
- Restrict list to current user or apply an extra query scope
- Works as a modelable Livewire input: `wire:model="media"`
- Attribute/config gating for actions: `canDelete`, `canSeeTrash`, `canRestoreTrash`
- Drag-and-drop upload and progress bar
- Fully self-styled with a packaged CSS build; no Tailwind/Bootstrap conflicts
 - Optional upload constraints: exact width/height and/or aspect ratio (e.g., 16:9)
 - Returns structured value on Insert: [{ id, collection, path }] for later attaching
 - Prefill support: pass the same payload to :value and items auto-select in the modal
 - Compatible with Spatie Media Library (separate trait and DB tables)
 - Persists and reads item order via pivot order_column (syncMedia + payload)
 - Optional collection scoping per component instance
 - Emits rich events (`media-added`, `media-uploaded`, `media-deleted`, `media-restored`, `media-selected`) with full item payloads
 
#### UX & i18n updates
- Action buttons are hidden by default and appear on hover (non-interactive when hidden)
- Clear, thicker selection ring with offset for better contrast
- Select File tab is the default when the modal opens
- New `can_upload` config and `:canUpload` attribute to disable Upload tab and uploads
- RTL support (auto when locale is Arabic/Kurdish/etc.); key positions flip in RTL
- Translations included (English, Arabic, Kurdish/Sorani) with publishable lang files
- Component inherits your app’s font-family

## Installation

### Requirements

- PHP >= 8.1
- Laravel 10–12
- Livewire 3.3+

Note: Livewire 3 requires Laravel 10+. If you need Laravel 9 support, a Livewire v2–compatible variant is required (not included in this package version).

Require the package:

```bash
composer require drpshtiwan/livewire-media-selector
```

Publish the config (optional):

```bash
php artisan vendor:publish --tag=media-selector-config
```

Publish the migration and run it:

```bash
php artisan vendor:publish --tag=media-selector-migrations
php artisan migrate
```

Ensure your `public` disk is set up and linked:

```bash
php artisan storage:link
```

Publish the views (optional, if you want to customize the markup/classes):

```bash
php artisan vendor:publish --tag=media-selector-views
```

Publish the assets (CSS):

```bash
php artisan vendor:publish --tag=media-selector-assets --force
```

### Include the stylesheet

Include styles in your app layout head, after Livewire styles:

```blade
@livewireStyles
@mediaSelectorStyles
```

Alternatively, inline the CSS:

```blade
@livewireStyles
@mediaSelectorStylesInline
```

 
### Internationalization (i18n) & RTL

Translations are bundled for English (`en`), Arabic (`ar`), and Kurdish/Sorani (`ckb`). Load and (optionally) publish them:

```php
// Service provider auto-loads translations from resources/lang
php artisan vendor:publish --tag=media-selector-lang
```

Use your app locale to switch languages; keys live under `media-selector::messages.*`.

RTL is applied automatically when the locale is one of: `ar`, `ckb`, `fa`, `ur`. The component adds `dir="rtl"` and flips critical positions.

## Configuration

Config file: `config/media-selector.php`

- `disk`: Filesystem disk to use (default: `public`)
- `directory`: Directory within the disk (default: `media`)
- `per_page`: Items per page (default: `24`)
- `max_upload_kb`: Max upload size in KB (default: `5120`)
- `allowed_extensions`: Allowed extensions (images, docs, video, audio)
- `allowed_mimes`: Allowed MIME types (supports wildcards, e.g. `image/*`, `video/*`, `audio/*`)
- `multiple`: Default multi-select mode
- `restrict_to_current_user`: Only show current user’s media
- `extra_scope`: Callable string to add custom query constraints
- `can_upload`: Enable/disable the Upload tab and uploading (default: `true`)
- `can_delete`: Enable delete controls
- `can_see_trash`: Show Trash tab (soft-deleted items)
- `can_restore_trash`: Enable restore actions in Trash
- `ui`: UI flavor for the modal (`tailwind` or `bootstrap`)

Database tables can be changed via:

```php
// config/media-selector.php
'table' => 'media_selector_media',
'mediables_table' => 'media_selector_mediables',
```

## Usage

Use the component inside any Blade view:

```blade
<livewire:media-selector wire:model="media" />
```

In your Livewire parent component:

```php
public string|array|null $media = null; // Holds storage path(s) for selected media
```

Notes:
- The bound value is an array of objects with `{ id, collection, path }` after you click Insert.
- Resolve a public URL with `Storage::disk(config('media-selector.disk'))->url($item['path'])`.
- For not-yet-created models: collect the array, create the model, then map `path` → media IDs and write to your own pivot via a trait or your domain logic.
- You can enable multi-select, delete, and trash restore via attributes (or rely on config defaults):

```blade
<livewire:media-selector
    wire:model="media"
    :multiple="true"
    :canDelete="true"
    :canSeeTrash="true"
    :canRestoreTrash="true"
    :canUpload="true"
    :restrictToCurrentUser="true"
    :requireWidth="3000"          {{-- optional: exact width --}}
    :requireHeight="3000"         {{-- optional: exact height --}}
    requireAspectRatio="16:9"      {{-- optional: ratio like 1:1, 4:3, 16:9 --}}
/> 
```

### Examples

#### 1) Single image avatar field

Blade (edit form):

```blade
<livewire:media-selector wire:model="avatar" :multiple="false" :canDelete="false" />
```

Livewire/Controller saving logic:

```php
// $this->avatar will be either null or an array like: [[ 'id' => 123, 'collection' => null, 'path' => 'media/abc.jpg' ]]
$payload = is_array($this->avatar) ? $this->avatar : [];
$image = collect($payload)->first();
$path = $image['path'] ?? null;
if ($path) {
    $user->avatar_path = $path;
    $user->save();
}
```

#### 2) Gallery with collection (attach to model)

Blade:

```blade
<livewire:media-selector wire:model="gallery" :multiple="true" collection="gallery" />
```

Model with trait and controller save:

```php
use DrPshtiwan\LivewireMediaSelector\Concerns\HasMediaSelector;

class Post extends Model
{
    use HasMediaSelector;
}

// Save selected gallery items (array of {id,collection,path})
$post->syncMedia($this->gallery, 'gallery');
```

#### 3) Filter types (mimes or extensions)

```blade
<livewire:media-selector wire:model="media" :multiple="true" mimes="['image/*']" />
<!-- or -->
<livewire:media-selector wire:model="media" :multiple="true" extensions="['jpg','png','webp']" />
```

#### 4) Enforce image constraints

```blade
<livewire:media-selector
    wire:model="cover"
    :multiple="false"
    :canUpload="true"
    :requireWidth="1200"
    :requireHeight="628"
    requireAspectRatio="1200:628"
/>
```

#### 5) Restrict listing to current user

```blade
<livewire:media-selector wire:model="files" :multiple="true" :restrictToCurrentUser="true" />
```

Or via config `config/media-selector.php`:

```php
'restrict_to_current_user' => true,
```

#### 6) Extra query scope (config)

Add a callable to further constrain the query:

```php
// config/media-selector.php
'extra_scope' => function ($query, $component) {
    $query->whereNull('collection'); // example: hide items with a collection
},
```

#### 7) Listen to browser events

The component dispatches browser events you can listen to with Alpine or vanilla JS.

```blade
<div x-data
     x-on:media-added.window="console.log('added:', $event.detail.items)"
     x-on:media-uploaded.window="console.log('uploaded:', $event.detail.items)"
     x-on:media-deleted.window="console.log('deleted:', $event.detail.id)"
     x-on:media-restored.window="console.log('restored:', $event.detail.id)">
    <livewire:media-selector wire:model="media" :multiple="true" />
</div>
```

#### 8) Bootstrap UI flavor

```php
// config/media-selector.php
'ui' => 'bootstrap',
```

Or per usage:

```blade
<livewire:media-selector wire:model="media" ui="bootstrap" />
```

Filter by type (mimes/extensions) and show only videos, for example:

```blade
<livewire:media-selector wire:model="media" :multiple="true" mimes="['video/*']" />
```

### Collections

Scope the library to a specific collection and have uploads saved into it:

```blade
<livewire:media-selector
    wire:model="media"
    collection="avatars"   {{-- list only items in this collection; new uploads saved to it --}}
/>
```

- If `collection` is omitted or empty, all media are listed.
- The value and events include `collection` along with `id` and `path` so you can persist it on your own pivots.

### Attaching to models via trait

Add the trait to your model and call `attachMedia`/`syncMedia` with the array returned from the selector (shape: `{ id, collection, path }`).

```php
use DrPshtiwan\LivewireMediaSelector\Concerns\HasMediaSelector;

class Post extends Model
{
    use HasMediaSelector; // distinct from Spatie's InteractsWithMedia
}

// Controller or component
$post->attachMedia($this->media, 'gallery');       // keep existing + add new
$post->syncMedia($this->media, 'gallery');         // replace existing in collection
$post->detachMedia([123, 456], 'gallery');         // remove by id(s)
// Read
$images = $post->getMedia('gallery');

// Pre-fill the selector with the same data shape it returns
$payload = $post->getMediaPayload('gallery');       // [{ id, collection, path }, ...]
// Blade
// <livewire:media-selector :value="$payload" />
```

### Events

- `media-added` with `items[]` (each item has `id,disk,path,url,filename,size,mime,width,height,collection`)
- `media-uploaded` with `items[]` (same shape)
- `media-deleted` with `id`
- `media-restored` with `id`
- `media-selected` with `path` and `url` (single selection flow)

Behavior notes
- Switching tabs clears the current selection to avoid accidental cross-tab actions
- Action buttons on tiles appear on hover and don’t capture clicks when hidden
- Uses isolated pagination (page param: `lmsPage`) to avoid conflicts with other Livewire components

## Styling

The component ships with its own Tailwind CSS build, scoped to the component to avoid conflicts with your app (Tailwind or Bootstrap). No global resets are applied.

- Isolation: styles are scoped under the component root using `[data-lms]` with Tailwind `important` and `preflight: false`.
- Minimal CSS: only utilities referenced in the package views are generated.
- Alpine: used for small interactions (toasts, drag/drop helpers).

You can publish the views and customize the markup/classes:

```bash
php artisan vendor:publish --tag=media-selector-views
```

Add a basic `x-cloak` helper if you use Alpine:

```html
<style>[x-cloak]{ display:none !important }</style>
```

Include the CSS after publishing it using either directive in your layout (after `@livewireStyles`):

```blade
@mediaSelectorStyles
```

or inline it:

```blade
@mediaSelectorStylesInline
```

Publish assets with: `php artisan vendor:publish --tag=media-selector-assets --force`.

## UI Flavors

You can switch between Tailwind (default) and Bootstrap markup variants.

- Via config (`config/media-selector.php`):

```php
'ui' => 'tailwind', // or 'bootstrap'
```

- Per component usage:

```blade
<livewire:media-selector wire:model="media" ui="bootstrap" />
```

## Security

- SVG uploads are allowed by default; remove `svg` from `allowed_extensions` if you don’t serve sanitized SVGs.
- Copy-to-clipboard uses the Clipboard API (requires HTTPS/localhost) with a fallback.
- Keep `storage:link` in place to generate public URLs for the selected media.

## Developer

Developed and maintained by [drpshtiwan](https://github.com/drpshtiwan).

## License

MIT License. See `LICENSE` for details.
