# Getting Started

This guide walks you through installing the package, publishing assets, and rendering the Livewire media selector in your application.

## Installation

```bash
composer require drpshtiwan/laravel-media-selector
```

Publish the default configuration and migrations if you need to customize them:

```bash
php artisan vendor:publish --tag=media-selector-config
php artisan vendor:publish --tag=media-selector-migrations
php artisan migrate
```

Publish the UI assets (Tailwind build) when you want to customize or version them yourself:

```bash
php artisan vendor:publish --tag=media-selector-assets --force
```

## Registering the trait

Attach media to any Eloquent model via the provided trait:

```php
use DrPshtiwan\LivewireMediaSelector\Concerns\HasMediaSelector;

class Post extends Model
{
    use HasMediaSelector;
}
```

The trait exposes helper methods such as `attachMedia`, `syncMedia`, `getMedia`, `getMediaUrls`, and `getMediaUrl`.

## Rendering the Livewire component

```blade
<livewire:media-selector
    wire:model="media"
    :multiple="true"
    collection="gallery"
    :can-upload="true"
    :can-delete="false"
/>
```

### Opening the modal programmatically

```php
Livewire::test(\DrPshtiwan\LivewireMediaSelector\Livewire\MediaSelector::class)
    ->call('openModal');
```

### Receiving selection data

When the user confirms their selection, the component updates `wire:model` with an array of items shaped like:

```json
[
  { "id": 15, "collection": "gallery", "path": "media/gallery/hero.jpg" }
]
```

You can store the payload or call `$post->syncMedia($payload, 'gallery');` to persist the relation.

