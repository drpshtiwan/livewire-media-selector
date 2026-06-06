# Configuration Reference

All configuration lives in `config/media-selector.php`.  
Below is an overview of the most relevant options for tailoring the selector to your project.

## Storage & directories

- `disk` — default filesystem disk used for uploads (`public`).
- `directory` — base directory relative to the disk root (`media`).
- `max_upload_kb` — upload size limit in kilobytes.

Use the trait helper `getMediaUrls()` or `getMediaUrl()` to resolve storage URLs regardless of the chosen disk.

## Upload restrictions

- `allowed_extensions` — array of extensions (e.g. `['jpg', 'png', 'webp']`).
- `allowed_mimes` — full mime types or wildcard groups (e.g. `['image/*']`).
- Provide `:extensions` or `:mimes` attributes on the component to override per-instance limits.

::: warning SVG uploads
`svg` is intentionally **omitted** from the default `allowed_extensions`. SVG files are XML and can embed `<script>`/event handlers, so serving an uploaded SVG from a public disk by URL is a stored-XSS vector. If you re-enable SVG, sanitize uploads (e.g. [`enshrined/svg-sanitize`](https://github.com/darylldoyle/svg-sanitizer)) or serve them with `Content-Disposition: attachment` and a restrictive CSP. Note the `image/*` MIME wildcard also matches `image/svg+xml`.
:::

## Component behavior

- `multiple` — allow multi-select.
- `can_upload` / `can_delete` — gate destructive actions.
- `can_see_trash` / `can_restore_trash` — expose soft-deleted media management.
- `restrict_to_current_user` — scope listings to the authenticated user’s uploads.
- `show_thumbnails` — toggles image thumbnails in the selected preview area below the Clear/Choose Media buttons. When disabled, that preview section is not rendered (modal grids are unaffected).

## Security model

The component treats permission and storage configuration as a **server-side trust boundary**:

- Permission flags (`can_delete`, `can_upload`, `can_see_trash`, `can_restore_trash`, `restrict_to_current_user`), the allowed file types, the storage `disk`/`directory`, and upload limits are exposed as `#[Locked]` Livewire properties. They are resolved once in `mount()` and **cannot be changed from the browser** — a crafted Livewire request cannot flip a permission, widen the allowed file types to smuggle an executable upload, or repoint storage.
- Always derive these flags from your own authorization, e.g. `:can-delete="auth()->user()?->can('delete', $model)"`. The package never grants an action you did not enable.
- Every selection, insertion, and deletion is re-validated server-side against the active, scoped query, so a user can never act on media outside the disk/collection/owner scope they are viewing.

## UI flavor

Use the Tailwind UI variant:

```php
'ui' => env('MEDIA_SELECTOR_UI', 'tailwind'),
```

Per component override:

```blade
<livewire:media-selector ui="tailwind" />
```

## Custom filtering

You can register an extra scope callback to modify the underlying query:

```php
'extra_scope' => App\MediaSelector\Scopes\TeamScoped::class.'@apply',
```

Within the `apply` method, receive the query builder and the Livewire component instance to add constraints:

```php
public function apply($query, $component)
{
    $query->where('team_id', $component->teamId);
}
```
