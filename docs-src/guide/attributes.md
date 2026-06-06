# Attribute Reference

Pass these attributes directly to the `<livewire:media-selector />` component to override configuration values on a per-instance basis.

| Attribute | Type | Default | Description |
|-----------|------|---------|-------------|
| `wire:model` / `wire:model.defer` | `array|string|null` | — | Binds the selected media payload to the parent component. Confirmed selections are emitted as `{ id, collection, path }` payload arrays (single-select typically returns one-item array; multi-select returns many). |
| `collection` | `string|null` | `null` | Filters the grid to a specific collection and ensures new uploads are saved under the same collection value. |
| `multiple` | `bool` | `config('media-selector.multiple', false)` | Enables multi-select mode. When `false`, the selector behaves like a single-value input. |
| `can-upload` / `:can-upload` | `bool` | `config('media-selector.can_upload', true)` | Toggles the Upload tab and the ability to upload new files. |
| `can-delete` / `:can-delete` | `bool` | `config('media-selector.can_delete', false)` | Shows delete buttons and enables soft-deleting media items. |
| `can-see-trash` / `:can-see-trash` | `bool` | `config('media-selector.can_see_trash', false)` | Exposes the Trash tab with soft-deleted media. |
| `can-restore-trash` / `:can-restore-trash` | `bool` | `config('media-selector.can_restore_trash', false)` | Allows restoring items from the Trash tab. |
| `restrict-to-current-user` / `:restrict-to-current-user` | `bool` | `config('media-selector.restrict_to_current_user', false)` | Limits the query to records owned by the authenticated user (`user_id = Auth::id()`). |
| `extensions` / `:extensions` | `array|string` | `config('media-selector.allowed_extensions', [...])` | Restricts uploads to specific file extensions. Accepts an array or comma-separated string (e.g. `"jpg,png,webp"`). |
| `mimes` / `:mimes` | `array|string` | `config('media-selector.allowed_mimes', [])` | Restricts uploads to specific MIME types (supports wildcards like `image/*`). Overrides extension rules when provided. |
| `ui` | `string` | `config('media-selector.ui', 'tailwind')` | UI variant. Currently supports `tailwind`. |
| `disk` | `string` | `config('media-selector.disk', 'public')` | Sets the filesystem disk used for uploads and URL generation. |
| `directory` | `string` | `config('media-selector.directory', 'media')` | Base directory on the chosen disk where uploads are stored. |
| `per-page` / `:per-page` | `int` | `config('media-selector.per_page', 24)` | Number of media items per page within the modal. |
| `max-upload-kb` / `:max-upload-kb` | `int` | `config('media-selector.max_upload_kb', 5120)` | Maximum upload size in kilobytes. |
| `show-thumbnails` / `:show-thumbnails` | `bool` | `config('media-selector.show_thumbnails', true)` | Shows or hides rendered image thumbnails in the selected preview area below Clear/Choose Media. When `false`, that preview area is not rendered; modal grids are unaffected. |
| `require-width` / `:require-width` | `int|null` | `null` | Enforces an exact image width (in pixels) during upload validation. |
| `require-height` / `:require-height` | `int|null` | `null` | Enforces an exact image height (in pixels). |
| `require-aspect-ratio` / `:require-aspect-ratio` | `string|null` | `null` | Validates uploaded images against a specific aspect ratio (e.g. `"16:9"`). |
| `value` | `array|string|null` | — | Pre-populates the selector when not using `wire:model`. Accepts the same payload shape returned by the component. |
| `selected-only` / `:selected-only` | `bool` | `false` | Forces the grid to display only the currently selected items. Useful for read-only previews. |
| `sort` | `string` | `'newest'` | Controls initial sort order. Supported values: `newest`, `oldest`, `name_asc`, `name_desc`. |
| `search` | `string` | `''` | Sets the initial search term for the media list. |
| `show-modal` / `:show-modal` | `bool` | `false` | Manually toggles the modal open/closed state. Usually managed internally via component methods. |
| `selected-ids` / `:selected-ids` | `array<int>` | `[]` | Pre-selects specific media IDs in the grid. Primarily used internally when syncing with the bound value. |

::: tip Attribute casing
When using Blade/Livewire, kebab-case attribute names (e.g. `can-upload`) map to the camelCase properties on the component.
:::

::: warning Permission attributes are server-authoritative
The permission and storage/validation attributes (`can-upload`, `can-delete`, `can-see-trash`, `can-restore-trash`, `restrict-to-current-user`, `mimes`, `extensions`, `disk`, `directory`, `per-page`, `max-upload-kb`, dimension constraints) back `#[Locked]` properties. They take effect at mount and **cannot be changed from the browser** — a crafted Livewire request cannot escalate permissions or loosen upload validation. Always derive the permission flags from your own authorization, e.g. `:can-delete="auth()->user()?->can('delete', $model)"`. See the [security model](./configuration.md#security-model) for details.
:::

### Query helper

The trait includes an Eloquent scope for eagerly loading media:

```php
$posts = Post::query()
    ->withMediaCollection('gallery', function ($relation) {
        $relation->orderBy('media.id');
    })
    ->get();
```

This scope loads the `media` relation (optionally filtered by collection) so repeated calls to `getMedia('gallery')` do not perform extra queries.

## Examples

### Basic single-select

```blade
<livewire:media-selector wire:model="heroImage" />
```

```php
public array|string|null $heroImage = null;

public function save(): void
{
    $this->post->syncMedia($this->heroImage, 'hero');
}
```

### Multi-select gallery with collection & ordering

```blade
<livewire:media-selector
    wire:model="gallery"
    collection="gallery"
    :multiple="true"
    :can-upload="true"
    :can-delete="true"
/>
```

```php
public array $gallery = [];

public function mount(Post $post): void
{
    $this->gallery = $post->getMediaPayload('gallery');
    $this->post = Post::query()
        ->withMediaCollection('gallery')
        ->findOrFail($post->getKey());
}

public function save(): void
{
    $this->post->syncMedia($this->gallery, 'gallery');
}
```

### Tailwind UI, video-only uploads

```blade
<livewire:media-selector
    wire:model="videos"
    ui="tailwind"
    mimes='["video/*"]'
    :multiple="true"
/>
```

### User-restricted library with trash management

```blade
<livewire:media-selector
    wire:model="documents"
    collection="docs"
    :restrict-to-current-user="true"
    :can-see-trash="true"
    :can-restore-trash="true"
/>
```

### Strict dimension requirements

```blade
<livewire:media-selector
    wire:model="banners"
    :multiple="true"
    :require-width="1920"
    :require-height="1080"
    require-aspect-ratio="16:9"
/>
```

Each example can be combined—attributes layer on top of configuration defaults so you can tailor the selector per use-case.

### Product model example (thumbnail + gallery)

Assuming your `Product` model uses the `HasMediaSelector` trait:

```php
class Product extends Model
{
    use HasMediaSelector;
}
```

In your Livewire component:

```php
class EditProduct extends Component
{
    public Product $product;
    public array|null $thumbnail = null;
    public array $gallery = [];

    public function mount(Product $product): void
    {
        $this->product = Product::query()
            ->withMediaCollection('gallery')
            ->findOrFail($product->getKey());

        $this->thumbnail = $product->getMediaPayload('thumbnail');
        $this->gallery = $product->getMediaPayload('gallery');
    }

    public function save(): void
    {
        $this->product->syncMedia($this->thumbnail, 'thumbnail');
        $this->product->syncMedia($this->gallery, 'gallery');

        $this->dispatch('product-saved');
    }
}
```

Blade view:

```blade
<div class="space-y-6">
    <div>
        <h3 class="font-semibold mb-2">Thumbnail</h3>
        <livewire:media-selector
            wire:model="thumbnail"
            collection="thumbnail"
            :multiple="false"
            :require-width="800"
            :require-height="600"
        />
    </div>

    <div>
        <h3 class="font-semibold mb-2">Gallery</h3>
        <livewire:media-selector
            wire:model="gallery"
            collection="gallery"
            :multiple="true"
            :can-upload="true"
            :can-delete="true"
        />
    </div>
</div>
```

Whenever you need to render images on the storefront:

```php
$thumbUrl = $product->getMediaUrl('thumbnail');
$galleryUrls = $product->getMediaUrls('gallery');
```

### Upload bindings

- `uploads` / `:uploads` — Bind an array of `UploadedFile` instances if you handle uploads manually. Typically managed by the component when users pick files via the modal.
- `accept` — Exposes the computed `accept` string used for `<input type="file" accept="...">`. Usually derived automatically from `mimes` and `extensions`.

### Events & outputs

- The component emits events such as `media-added`, `media-uploaded`, `media-deleted`, `media-restored`, and `media-selected`.  
- Use the trait helpers on your models (`getMedia`, `getMediaUrls`, `getMediaUrl`, `attachMedia`, `syncMedia`) to work with the stored payloads.
