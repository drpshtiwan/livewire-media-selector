# Changelog

All notable changes to `drpshtiwan/livewire-media-selector` are documented in this file.

## [Unreleased]

### Added
- Support for **Laravel 12 and 13** (`illuminate/*` `^12.0|^13.0`). Dropped Laravel 10 and 11 (Laravel 11 reached its security-fix EOL in March 2026).
- Added a regression test asserting locked properties reject client-side mutation.

### Security
- **Locked all permission and storage/validation properties** (`canDelete`, `canUpload`, `canSeeTrash`, `canRestoreTrash`, `restrictToCurrentUser`, `mimes`/`extensions`, `allowedMimes`/`allowedExtensions`, `disk`, `directory`, `perPage`, `maxUploadKb`, dimension constraints, `collection`, `ui`, `accept`, `showThumbnails`) with Livewire's `#[Locked]` attribute. Previously these were ordinary public properties, so a crafted Livewire request could flip permissions, empty the allowed-type lists to bypass upload validation (e.g. upload an executable file), or repoint the storage disk/directory. They are now resolved server-side in `mount()` only.
- Removed `svg` from the default `allowed_extensions` and documented the stored-XSS risk of serving uploaded SVGs from a public disk.

### Compatibility
- Minimum PHP raised to **8.3**. Minimum Livewire raised to 3.5.
- Dev dependencies updated: `orchestra/testbench` `^10|^11`, `pestphp/pest` `^3|^4`.
- CI matrix now runs PHP 8.3–8.4 across Laravel 12 and 13.

## 3.0.0

### Added
- Livewire 4 compatibility is maintained, including event-dispatch behavior coverage in tests.
- Added `show_thumbnails` config option (`MEDIA_SELECTOR_SHOW_THUMBNAILS`) to control thumbnail rendering globally.
- Added `show-thumbnails` / `:show-thumbnails` per-component attribute override.
- Added tests for thumbnail visibility override.

### Changed
- Selection, confirmation, delete, restore, and force-delete actions now use scoped queries (disk/user/collection/extra scope) for safer access control.
- Added per-component mount overrides for `disk`, `directory`, `perPage`, and `maxUploadKb`.
- Media model now respects configured `media-selector.table`.
- `HasMediaSelector` relation now respects configured `media-selector.model`.
- Preview rendering now uses safe URL/MIME helpers to avoid exceptions when files are missing.
- Upload extension validation now fully follows configured `allowed_extensions`.
- Pagination reset in trash actions now consistently uses `lmsPage`.
- UI mode documentation/configuration now reflects Tailwind-only support.
- `show_thumbnails=false` now skips rendering the selected-preview section below Clear/Choose Media (modal grids still render thumbnails).


### Documentation
- Fixed docs install command to use `composer require drpshtiwan/livewire-media-selector`.
- Added docs for `show_thumbnails` and `show-thumbnails`.
- Synced attribute docs with actual `wire:model` payload behavior.

### Planned Features
- Authorization policy integration (Gate/Policy hooks per action/item).
- Pluggable UI templates/slots for media card rendering.
- Advanced uploads (chunked, resumable, direct-to-cloud options).
- Media editing tools (crop/rotate/resize before save).
- Rich organization (folders, tags, favorites, metadata filters).
- Accessibility upgrades (focus trap and stronger keyboard navigation).
- Bulk actions (move, download, metadata edit, select-all workflows).
- Optional strict single-select payload mode (single object output).
