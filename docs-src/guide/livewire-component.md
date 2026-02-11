# Livewire Integration

The media selector ships as a Livewire 3/4 component and is optimized for parent-child data binding.

## Using `wire:model`

```blade
<livewire:media-selector wire:model="postMedia" />
```

The component’s `value` property syncs with your parent component or Blade context.  
When the modal closes with a confirmed selection, the payload updates automatically.

## Interacting from a Livewire parent

```php
class EditPost extends Component
{
    public array $postMedia = [];

    public function mount(Post $post): void
    {
        $this->postMedia = $post->getMediaPayload('gallery');
    }

    public function save(): void
    {
        $this->validate([
            'postMedia' => ['array'],
        ]);

        $this->post->syncMedia($this->postMedia, 'gallery');
    }
}
```

### Listen for events

The component emits several events:

- `media-added`
- `media-uploaded`
- `media-deleted`
- `media-restored`
- `media-selected`

Handling an event:

```php
protected $listeners = [
    'media-selected' => 'handleMediaSelected',
];

public function handleMediaSelected($payload): void
{
    ray('Selected', $payload);
}
```

## Accessing URLs directly

When rendering preview thumbnails outside the selector, use the trait helpers:

```php
$post->getMediaUrl('gallery'); // first URL
$post->getMediaUrls('gallery'); // all URLs
```

This ensures cache-friendly lookups that respect eager-loaded relations and disk-specific URL generation.
