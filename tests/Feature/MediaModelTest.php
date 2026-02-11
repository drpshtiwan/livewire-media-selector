<?php

use DrPshtiwan\LivewireMediaSelector\Models\Media;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

it('creates media and exposes url and extension accessors', function () {
    Storage::fake('public');

    $path = 'media/example.jpg';
    Storage::disk('public')->put($path, 'fake');

    $m = Media::create([
        'user_id' => null,
        'disk' => 'public',
        'path' => $path,
        'filename' => 'example.jpg',
        'collection' => 'tests',
        'mime' => 'image/jpeg',
        'size' => 4,
        'width' => 1,
        'height' => 1,
        'metadata' => ['original' => 'example.jpg'],
    ]);

    expect($m->url)->toBeString()->toContain('/');
    expect($m->extension)->toBe('jpg');
});

it('scopes by disk, collection and can search and previewable', function () {
    Storage::fake('public');

    $m1 = Media::create([
        'disk' => 'public',
        'path' => 'media/a.jpg',
        'filename' => 'a.jpg',
        'collection' => 'alpha',
        'mime' => 'image/jpeg',
        'size' => 1,
    ]);
    $m2 = Media::create([
        'disk' => 'public',
        'path' => 'media/audio.mp3',
        'filename' => 'audio.mp3',
        'collection' => 'beta',
        'mime' => 'audio/mpeg',
        'size' => 1,
    ]);

    $byDisk = Media::query()->forDisk('public')->pluck('id');
    expect($byDisk)->toContain($m1->id, $m2->id);

    $byCollection = Media::query()->forCollection('alpha')->pluck('id');
    expect($byCollection)->toContain($m1->id)->not()->toContain($m2->id);

    $search = Media::query()->search('audio')->pluck('id');
    expect($search)->toContain($m2->id)->not()->toContain($m1->id);

    $previewable = Media::query()->previewable()->pluck('id');
    expect($previewable)->toContain($m1->id)->not()->toContain($m2->id);
});

it('uses configured table name for media model', function () {
    Schema::dropIfExists('custom_media_table');
    Schema::create('custom_media_table', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id')->nullable();
        $table->string('disk');
        $table->string('path');
        $table->string('filename');
        $table->string('collection')->nullable();
        $table->string('mime')->nullable();
        $table->unsignedBigInteger('size')->default(0);
        $table->unsignedInteger('width')->nullable();
        $table->unsignedInteger('height')->nullable();
        $table->json('metadata')->nullable();
        $table->timestamps();
        $table->softDeletes();
    });

    config()->set('media-selector.table', 'custom_media_table');

    $media = new Media;
    expect($media->getTable())->toBe('custom_media_table');

    Media::create([
        'disk' => 'public',
        'path' => 'media/custom.jpg',
        'filename' => 'custom.jpg',
        'collection' => null,
        'mime' => 'image/jpeg',
        'size' => 1,
    ]);

    expect(Schema::hasTable('custom_media_table'))->toBeTrue();
    expect(Media::query()->count())->toBe(1);

    Schema::dropIfExists('custom_media_table');
    config()->set('media-selector.table', 'media_selector_media');
});
