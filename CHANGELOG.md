# Changelog

All notable changes to `drpshtiwan/livewire-media-selector` are documented in this file.

## [Unreleased]

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
