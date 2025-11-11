# Testing Recipes

The package ships with Pest-powered feature tests.  
Use these examples to test your own integrations and keep regressions out of production.

## Faking storage

```php
Storage::fake('public');

$media = Media::create([
    'disk' => 'public',
    'path' => 'media/example.jpg',
    'filename' => 'example.jpg',
    'collection' => 'gallery',
    'mime' => 'image/jpeg',
]);

expect(Storage::disk('public')->exists($media->path))->toBeTrue();
```

## Livewire component tests

```php
Livewire::test(MediaSelector::class)
    ->call('openModal')
    ->assertSet('showModal', true)
    ->set('selectedIds', [$media->id])
    ->call('closeModal');
```

## Trait helpers

```php
$post = TestHasMediaModel::create();
$post->attachMedia([['id' => $media->id]], 'gallery');

expect($post->getMediaUrl('gallery'))->toEndWith('example.jpg');
```

### Assertions

- `assertSee()` for modal labels and translated strings.
- `assertHasErrors()` when validating uploads.
- `assertSet()` to verify Livewire property mutations.

Run the entire suite with:

```bash
composer test
```

