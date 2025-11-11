# Livewire Media Selector

[![Packagist Version](https://img.shields.io/packagist/v/drpshtiwan/livewire-media-selector.svg?style=flat-square)](https://packagist.org/packages/drpshtiwan/livewire-media-selector)
[![Total Downloads](https://img.shields.io/packagist/dt/drpshtiwan/livewire-media-selector.svg?style=flat-square)](https://packagist.org/packages/drpshtiwan/livewire-media-selector)
[![License](https://img.shields.io/packagist/l/drpshtiwan/livewire-media-selector.svg?style=flat-square)](LICENSE)
[![Tests](https://img.shields.io/github/actions/workflow/status/drpshtiwan/livewire-media-selector/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/drpshtiwan/livewire-media-selector/actions)
[![Code Style: Laravel Pint](https://img.shields.io/badge/code%20style-Laravel%20Pint-FF2D20?style=flat-square)](https://github.com/laravel/pint)
[![PHP Version](https://img.shields.io/packagist/php-v/drpshtiwan/livewire-media-selector?style=flat-square)](composer.json)

A lightweight, WordPress-style media selector for Laravel applications powered by Livewire.  
Full documentation: [livewire-media.thejano.com](http://livewire-media.thejano.com/)


## Screenshots

![Media selector preview](assets/image.png)

### Video demo

[![Watch the demo on YouTube](https://img.youtube.com/vi/4Snjk2213ls/hqdefault.jpg)](https://www.youtube.com/watch?v=4Snjk2213ls)

### Features

- Browse, search, and paginate media stored on your Laravel disks
- Upload new files (respecting size, extension, and mime limits)
- Single or multiple selection with drag-to-reorder support
- Optional collections to group media per feature (e.g. `gallery`, `avatars`)
- Trait helpers (`attachMedia`, `syncMedia`, `getMediaUrl`) for quick model integration
- Soft delete, restore, and optional trash tab when you need moderation
- Emits Livewire/browser events so you can react to uploads, deletes, and selections
 
#### UX & i18n updates
- Action buttons are hidden by default and appear on hover (non-interactive when hidden)
- Clear, thicker selection ring with offset for better contrast
- Select File tab is the default when the modal opens
- New `can_upload` config and `:canUpload` attribute to disable Upload tab and uploads
- RTL support (auto when locale is Arabic/Kurdish/etc.); key positions flip in RTL
- Translations included (English, Arabic, Kurdish/Sorani) with publishable lang files
- Component inherits your app’s font-family

## Installation

### Requirements

- PHP >= 8.1
- Laravel 10–12
- Livewire 3.3+

Note: Livewire 3 requires Laravel 10+. If you need Laravel 9 support, a Livewire v2–compatible variant is required (not included in this package version).

Require the package:

```bash
composer require drpshtiwan/livewire-media-selector
```

Publish the config (optional):

```bash
php artisan vendor:publish --tag=media-selector-config
```

Publish the migration and run it:

```bash
php artisan vendor:publish --tag=media-selector-migrations
php artisan migrate
```

Ensure your `public` disk is set up and linked:

```bash
php artisan storage:link
```

Publish the views (optional, if you want to customize the markup/classes):

```bash
php artisan vendor:publish --tag=media-selector-views
```

Publish the assets (CSS):

```bash
php artisan vendor:publish --tag=media-selector-assets --force
```

Simple usage:

```blade
<livewire:media-selector wire:model="media" collection="gallery" />
```

[Read the docs](http://livewire-media.thejano.com/) for setup details, configuration options, and integration patterns.

## Developer

Developed and maintained by [drpshtiwan](https://github.com/drpshtiwan).

## License

MIT License. See `LICENSE` for details.
