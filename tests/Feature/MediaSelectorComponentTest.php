<?php

use DrPshtiwan\LivewireMediaSelector\Livewire\MediaSelector;
use DrPshtiwan\LivewireMediaSelector\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;

it('renders and can open modal and list media', function () {
    Storage::fake('public');

    // seed a couple items
    $a = Media::create([
        'disk' => 'public',
        'path' => 'media/a.jpg',
        'filename' => 'a.jpg',
        'collection' => null,
        'mime' => 'image/jpeg',
        'size' => 1,
    ]);
    $b = Media::create([
        'disk' => 'public',
        'path' => 'media/b.pdf',
        'filename' => 'b.pdf',
        'collection' => null,
        'mime' => 'application/pdf',
        'size' => 1,
    ]);

    $comp = Livewire::test(MediaSelector::class, [
        'multiple' => true,
        'canDelete' => false,
        'canUpload' => false,
    ]);

    // Initially closed
    $comp->assertSet('showModal', false);

    // Open and ensure modal state and translation text present
    $comp->call('openModal')
        ->assertSet('showModal', true)
        ->assertSet('activeTab', 'browse')
        ->assertSee(trans('media-selector::messages.media_library'));

    // Ensure media listing is populated
    $items = $comp->get('media');
    expect($items->total())->toBe(2);
});

it('stores actual mime type when client reports octet stream', function () {
    Storage::fake('public');

    $spoofed = UploadedFile::fake()
        ->image('photo.jpg', 10, 10)
        ->size(120)
        ->mimeType('application/octet-stream');

    $actualMime = mime_content_type($spoofed->getPathname());

    Livewire::test(MediaSelector::class, [
        'canUpload' => true,
    ])
        ->set('uploads', [$spoofed])
        ->call('saveUpload')
        ->assertHasNoErrors();

    $media = Media::first();

    expect($media)->not->toBeNull();
    expect($media->mime)->toBe($actualMime);
    expect(Storage::disk('public')->exists($media->path))->toBeTrue();
});

it('dispatches selection event with payload in v4', function () {
    Storage::fake('public');

    $path = 'media/a.jpg';
    Storage::disk('public')->put($path, 'x');
    $url = Storage::disk('public')->url($path);

    Livewire::test(MediaSelector::class)
        ->call('selectMedia', $path)
        ->assertSet('value', $path)
        ->assertDispatched('media-selected', path: $path, url: $url);
});

it('does not allow selecting media outside active scope', function () {
    $inScope = Media::create([
        'disk' => 'public',
        'path' => 'media/in-scope.jpg',
        'filename' => 'in-scope.jpg',
        'collection' => 'gallery',
        'mime' => 'image/jpeg',
        'size' => 1,
    ]);
    $outOfScope = Media::create([
        'disk' => 'private',
        'path' => 'media/out-of-scope.jpg',
        'filename' => 'out-of-scope.jpg',
        'collection' => 'gallery',
        'mime' => 'image/jpeg',
        'size' => 1,
    ]);

    Livewire::test(MediaSelector::class, [
        'disk' => 'public',
        'collection' => 'gallery',
        'multiple' => true,
    ])
        ->call('openModal')
        ->call('toggleSelect', $inScope->id)
        ->assertSet('selectedIds', [$inScope->id])
        ->call('toggleSelect', $outOfScope->id)
        ->assertHasErrors(['selection'])
        ->assertSet('selectedIds', [$inScope->id]);
});

it('does not confirm selection for media outside active scope', function () {
    $inScope = Media::create([
        'disk' => 'public',
        'path' => 'media/in-scope.jpg',
        'filename' => 'in-scope.jpg',
        'collection' => 'gallery',
        'mime' => 'image/jpeg',
        'size' => 1,
    ]);
    $outOfScope = Media::create([
        'disk' => 'private',
        'path' => 'media/out-of-scope.jpg',
        'filename' => 'out-of-scope.jpg',
        'collection' => 'gallery',
        'mime' => 'image/jpeg',
        'size' => 1,
    ]);

    Livewire::test(MediaSelector::class, [
        'disk' => 'public',
        'collection' => 'gallery',
        'multiple' => true,
    ])
        ->set('selectedIds', [$inScope->id, $outOfScope->id])
        ->call('confirmSelection')
        ->assertHasErrors(['selection'])
        ->assertSet('value', []);
});

it('does not delete media outside active scope', function () {
    $inScope = Media::create([
        'disk' => 'public',
        'path' => 'media/in-scope.jpg',
        'filename' => 'in-scope.jpg',
        'collection' => 'gallery',
        'mime' => 'image/jpeg',
        'size' => 1,
    ]);
    $outOfScope = Media::create([
        'disk' => 'private',
        'path' => 'media/out-of-scope.jpg',
        'filename' => 'out-of-scope.jpg',
        'collection' => 'gallery',
        'mime' => 'image/jpeg',
        'size' => 1,
    ]);

    Livewire::test(MediaSelector::class, [
        'disk' => 'public',
        'collection' => 'gallery',
        'canDelete' => true,
    ])
        ->call('deleteMedia', $outOfScope->id)
        ->call('deleteMedia', $inScope->id);

    expect(Media::query()->find($outOfScope->id))->not->toBeNull();
    expect(Media::query()->find($inScope->id))->toBeNull();
    expect(Media::withTrashed()->find($inScope->id))->not->toBeNull();
});

it('rejects client attempts to escalate permission and config properties', function () {
    // Permission flags and storage/validation config are #[Locked]: a crafted
    // Livewire request must not be able to mutate them from the browser.
    $locked = [
        'canDelete' => true,
        'canUpload' => true,
        'canSeeTrash' => true,
        'canRestoreTrash' => true,
        'restrictToCurrentUser' => false,
        'allowedMimes' => [],
        'allowedExtensions' => [],
        'disk' => 'private',
        'directory' => '../escape',
        'maxUploadKb' => 999999,
    ];

    foreach ($locked as $property => $value) {
        $component = Livewire::test(MediaSelector::class, ['canDelete' => false, 'canUpload' => false]);

        expect(fn () => $component->set($property, $value))
            ->toThrow(CannotUpdateLockedPropertyException::class);
    }
});

it('accepts per component storage and pagination overrides', function () {
    Livewire::test(MediaSelector::class, [
        'disk' => 'public',
        'directory' => 'custom-media',
        'perPage' => 7,
        'maxUploadKb' => 1234,
    ])
        ->assertSet('disk', 'public')
        ->assertSet('directory', 'custom-media')
        ->assertSet('perPage', 7)
        ->assertSet('maxUploadKb', 1234);
});

it('accepts thumbnail visibility override', function () {
    Storage::fake('public');
    Storage::disk('public')->put('media/no-thumb.jpg', 'x');

    Media::create([
        'disk' => 'public',
        'path' => 'media/no-thumb.jpg',
        'filename' => 'no-thumb.jpg',
        'collection' => null,
        'mime' => 'image/jpeg',
        'size' => 1,
    ]);

    Livewire::test(MediaSelector::class, [
        'value' => 'media/no-thumb.jpg',
        'showThumbnails' => false,
    ])
        ->assertSet('showThumbnails', false)
        ->assertDontSee('lms-mt-3 lms-grid lms-grid-cols-2 md:lms-grid-cols-3 lg:lms-grid-cols-4 lms-gap-2')
        ->call('openModal')
        ->assertSeeHtml('<img');
});
