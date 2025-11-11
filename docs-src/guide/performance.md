# Performance Playbook

Fast media selection requires careful attention to both backend queries and frontend payloads.  
This page lists the optimizations implemented in the package and what you can do to keep things snappy.

## Avoiding N+1 queries

### Looping through posts with eager-load

```php
$posts = Post::query()
    ->withMediaCollection('gallery')
    ->latest()
    ->take(12)
    ->get();

foreach ($posts as $post) {
    $images = $post->getMedia('gallery');    // no additional queries
    $firstUrl = $post->getMediaUrl('gallery');

    // render a thumbnail list or hero image
}
```

- The `HasMediaSelector` trait respects eager-loaded relationships, so the loop above does not trigger extra DB hits.
- `getMedia()` checks `relationLoaded('media')` to reuse the in-memory collection.
- `getMediaUrls()` and `getMediaUrl()` leverage the cached relation when available.
- Pass a callback as the second parameter of `withMediaCollection()` to order or further constrain the eager-loaded relation.

## Query scopes

### Filtering media in bulk jobs

```php
$expired = Media::query()
    ->forDisk('public')
    ->forCollection('tmp-uploads')
    ->whereDate('created_at', '<', now()->subDay())
    ->pluck('id');

Media::whereIn('id', $expired)->delete();
```

Combine scope helpers with Laravel scheduler jobs to keep temporary collections small.

## Caching & CDNs

```php
// config/filesystems.php
'disks' => [
    'media_cdn' => [
        'driver' => 's3',
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION'),
        'bucket' => env('AWS_BUCKET'),
        'url' => env('AWS_URL'), // CloudFront or custom CDN domain
    ],
],
```

```php
// config/media-selector.php
'disk' => env('MEDIA_SELECTOR_DISK', 'media_cdn'),
```

- The media selector reads URLs from the configured disk; pointing the disk at a CDN ensures generated links are cacheable.
- For image-heavy galleries, store pre-generated thumbnails in a secondary collection (e.g. `gallery-thumbs`) and reference that in your app UI.

## Thumbnail rendering

```php
// config/media-selector.php
'preview_mimes' => [
    'image/jpeg',
    'image/png',
    'image/webp',
],
```

```php
<livewire:media-selector
    wire:model="gallery"
    collection="gallery"
    :require-width="800"
    :require-height="600"
/>
```

- Constrain previewable MIME types to avoid rendering heavy video assets in the grid.
- Enforce dimensions for uploads when you need consistent aspect ratios across the UI.

## Upload throughput

### Large file uploads component snippet

```blade
<livewire:media-selector
    wire:model="videos"
    collection="course-videos"
    :multiple="true"
    :max-upload-kb="102400"   {{-- 100 MB --}}
    mimes='["video/mp4","video/webm"]'
/>
```

- Leverage Livewire’s chunked uploads (handled internally by `HandlesUploads`) for large files.
- Surface limits in UI copy so users understand the allowed file size and type boundaries.

## Database hygiene

### Cleaning orphaned pivots

```php
Schedule::command('media-selector:prune-temp')
    ->daily();
```

```php
Artisan::command('media-selector:prune-temp', function () {
    $count = DB::table('media_selector_mediables')
        ->whereNotIn('media_id', Media::query()->select('id'))
        ->delete();

    $this->info("Removed {$count} orphaned pivot rows.");
});
```

- `syncMedia()` already removes stale attachments when updating a single model; scheduled jobs keep global tables tidy.
- Add DB indexes on `media_selector_media.collection`, `media_selector_media.disk`, and `media_selector_mediables.media_id` for large datasets.

