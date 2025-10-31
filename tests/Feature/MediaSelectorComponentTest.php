<?php

use DrPshtiwan\LivewireMediaSelector\Livewire\MediaSelector;
use DrPshtiwan\LivewireMediaSelector\Models\Media;
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
