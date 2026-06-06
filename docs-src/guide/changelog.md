# Changelog

This page summarizes recent package updates.

## Unreleased

### Added
- Support for **Laravel 12 and 13** (dropped Laravel 10 and 11).
- `show_thumbnails` config option and `show-thumbnails` component attribute.

### Security
- Permission and storage/validation properties are now `#[Locked]` and resolved server-side only, so the browser can no longer flip permissions, bypass upload-type validation, or repoint storage.
- `svg` removed from default `allowed_extensions` (stored-XSS risk when served from a public disk).

### Compatibility
- Minimum PHP raised to 8.3; minimum Livewire raised to 3.5.
- CI now tests PHP 8.3–8.4 across Laravel 12–13.

### Fixed
- `show_thumbnails=false` disables image rendering only in selected previews below Clear/Choose Media; modal grids still render thumbnails.

### Changed
- Action methods now enforce scoped media access (disk/user/collection/extra scope).
- Per-instance overrides now support `disk`, `directory`, `per-page`, and `max-upload-kb`.
- Media model and trait wiring now respect configured `table` and `model`.
- Preview handling is safer for missing files via guarded MIME/URL lookup.
- Trash pagination reset is consistent with `lmsPage`.
- Documentation now reflects Tailwind-only UI support.
- `show_thumbnails=false` now skips rendering the selected-preview section below Clear/Choose Media.
- Livewire 4 compatibility remains supported, including event dispatch behavior coverage.

### Docs
- Install command corrected to `composer require drpshtiwan/livewire-media-selector`.
- Attribute docs aligned with current `wire:model` payload behavior.

### Planned Features
- Authorization policy integration (Gate/Policy hooks per action/item).
- Pluggable UI templates/slots for media card rendering.
- Advanced uploads (chunked, resumable, direct-to-cloud options).
- Media editing tools (crop/rotate/resize before save).
- Rich organization (folders, tags, favorites, metadata filters).
- Accessibility upgrades (focus trap and stronger keyboard navigation).
- Bulk actions (move, download, metadata edit, select-all workflows).
- Optional strict single-select payload mode (single object output).

For full details, see the root package changelog: [`CHANGELOG.md`](../../../CHANGELOG.md).
