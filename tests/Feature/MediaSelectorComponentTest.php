<?php

use DrPshtiwan\LivewireMediaSelector\Livewire\MediaSelector;
use DrPshtiwan\LivewireMediaSelector\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
