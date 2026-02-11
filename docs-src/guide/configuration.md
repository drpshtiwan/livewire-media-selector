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

## Component behavior

- `multiple` — allow multi-select.
- `can_upload` / `can_delete` — gate destructive actions.
- `can_see_trash` / `can_restore_trash` — expose soft-deleted media management.
- `restrict_to_current_user` — scope listings to the authenticated user’s uploads.
- `show_thumbnails` — toggles image thumbnails in the selected preview area below the Clear/Choose Media buttons. When disabled, that preview section is not rendered (modal grids are unaffected).

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
