<?php

use DrPshtiwan\LivewireMediaSelector\Concerns\HasMediaSelector;
use DrPshtiwan\LivewireMediaSelector\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Schema::dropIfExists('test_models');

    Schema::create('test_models', function (Blueprint $table) {
        $table->id();
        $table->timestamps();
    });
});

afterEach(function () {
    Schema::dropIfExists('test_models');
});

it('returns loaded media without issuing extra queries', function () {
    $model = TestHasMediaModel::create();
    $media = Media::create([
        'disk' => 'public',
        'path' => 'media/example.jpg',
        'filename' => 'example.jpg',
        'mime' => 'image/jpeg',
        'size' => 1,
    ]);
    $model->media()->attach($media->id, ['collection' => null, 'order_column' => 1]);

    $model->load('media');

    DB::connection()->enableQueryLog();
    DB::flushQueryLog();

    $loaded = $model->getMedia();

    $queries = DB::getQueryLog();
    DB::connection()->disableQueryLog();

    expect($loaded)->toHaveCount(1);
    expect($loaded)->toBeInstanceOf(Collection::class);
    expect($queries)->toBeEmpty();
});

it('filters loaded media by collection without hitting the database', function () {
    $model = TestHasMediaModel::create();
    $imageA = Media::create([
        'disk' => 'public',
        'path' => 'media/a.jpg',
        'filename' => 'a.jpg',
        'collection' => 'gallery',
        'mime' => 'image/jpeg',
        'size' => 1,
    ]);
    $imageB = Media::create([
        'disk' => 'public',
        'path' => 'media/b.jpg',
        'filename' => 'b.jpg',
        'collection' => 'documents',
        'mime' => 'image/jpeg',
        'size' => 1,
    ]);
    $model->media()->attach($imageA->id, ['collection' => 'gallery', 'order_column' => 1]);
    $model->media()->attach($imageB->id, ['collection' => 'documents', 'order_column' => 2]);

    $model->load('media');

    DB::connection()->enableQueryLog();
    DB::flushQueryLog();

    $filtered = $model->getMedia('gallery');

    $queries = DB::getQueryLog();
    DB::connection()->disableQueryLog();

    expect($filtered)->toHaveCount(1);
    expect($filtered->first()->id)->toBe($imageA->id);
    expect($queries)->toBeEmpty();
});

it('can return media urls', function () {
    Storage::fake('public');

    $model = TestHasMediaModel::create();
    $imageA = Media::create([
        'disk' => 'public',
        'path' => 'media/a.jpg',
        'filename' => 'a.jpg',
        'collection' => 'gallery',
        'mime' => 'image/jpeg',
        'size' => 1,
    ]);
    $imageB = Media::create([
        'disk' => 'public',
        'path' => 'media/b.jpg',
        'filename' => 'b.jpg',
        'collection' => 'documents',
        'mime' => 'image/jpeg',
        'size' => 1,
    ]);

    $model->media()->attach($imageA->id, ['collection' => 'gallery', 'order_column' => 1]);
    $model->media()->attach($imageB->id, ['collection' => 'documents', 'order_column' => 2]);

    $allUrls = $model->getMediaUrls();
    $galleryUrls = $model->getMediaUrls('gallery');

    /** @var FilesystemAdapter $disk */
    $disk = Storage::disk('public');
    $expectedA = $disk->url('media/a.jpg');
    $expectedB = $disk->url('media/b.jpg');

    expect($allUrls)->toEqual([$expectedA, $expectedB]);
    expect($galleryUrls)->toEqual([$expectedA]);
});

it('can return the first media url shortcut', function () {
    Storage::fake('public');

    $model = TestHasMediaModel::create();
    $imageA = Media::create([
        'disk' => 'public',
        'path' => 'media/a.jpg',
        'filename' => 'a.jpg',
        'collection' => 'gallery',
        'mime' => 'image/jpeg',
        'size' => 1,
    ]);
    $imageB = Media::create([
        'disk' => 'public',
        'path' => 'media/b.jpg',
        'filename' => 'b.jpg',
        'collection' => 'gallery',
        'mime' => 'image/jpeg',
        'size' => 1,
    ]);

    $model->media()->attach($imageA->id, ['collection' => 'gallery', 'order_column' => 1]);
    $model->media()->attach($imageB->id, ['collection' => 'gallery', 'order_column' => 2]);

    $firstUrl = $model->getMediaUrl('gallery');
    $allFirst = $model->getMediaUrl();

    /** @var FilesystemAdapter $disk */
    $disk = Storage::disk('public');
    $expectedA = $disk->url('media/a.jpg');

    expect($firstUrl)->toBe($expectedA);
    expect($allFirst)->toBe($expectedA);
});

it('provides a scope to eager load media collections', function () {
    $model = TestHasMediaModel::create();
    $gallery = Media::create([
        'disk' => 'public',
        'path' => 'media/gallery.jpg',
        'filename' => 'gallery.jpg',
        'collection' => 'gallery',
        'mime' => 'image/jpeg',
        'size' => 1,
    ]);
    $doc = Media::create([
        'disk' => 'public',
        'path' => 'media/doc.pdf',
        'filename' => 'doc.pdf',
        'collection' => 'docs',
        'mime' => 'application/pdf',
        'size' => 1,
    ]);

    $model->media()->attach($gallery->id, ['collection' => 'gallery', 'order_column' => 1]);
    $model->media()->attach($doc->id, ['collection' => 'docs', 'order_column' => 2]);

    /** @var TestHasMediaModel $loaded */
    $loaded = TestHasMediaModel::query()->withMediaCollection('gallery')->first();

    expect($loaded)->not->toBeNull();
    expect($loaded->relationLoaded('media'))->toBeTrue();
    expect($loaded->media)->toHaveCount(1);
    expect($loaded->media->first()->id)->toBe($gallery->id);

    DB::connection()->enableQueryLog();
    DB::flushQueryLog();

    $loaded->getMedia('gallery');

    $queries = DB::getQueryLog();
    DB::connection()->disableQueryLog();

    expect($queries)->toBeEmpty();
});

it('uses configured media model class for the morph relation', function () {
    config()->set('media-selector.model', TestCustomMedia::class);

    $model = new TestHasMediaModel;
    $related = $model->media()->getRelated();

    expect($related)->toBeInstanceOf(TestCustomMedia::class);
});

class TestHasMediaModel extends Model
{
    use HasMediaSelector;

    protected $table = 'test_models';

    protected $guarded = [];
}

class TestCustomMedia extends Media {}
