<?php

namespace DrPshtiwan\LivewireMediaSelector;

use DrPshtiwan\LivewireMediaSelector\Livewire\MediaSelector;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class MediaSelectorServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/media-selector.php', 'media-selector');
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'media-selector');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'media-selector');

        if (class_exists(Livewire::class)) {
            Livewire::component('media-selector', MediaSelector::class);
        }

        $this->publishes([
            __DIR__.'/../config/media-selector.php' => config_path('media-selector.php'),
        ], 'media-selector-config');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/media-selector'),
        ], 'media-selector-views');

        $this->publishes([
            __DIR__.'/../resources/lang' => lang_path('vendor/media-selector'),
        ], 'media-selector-lang');

        $this->publishes([
            __DIR__.'/../stubs/MediaModel.php.stub' => app_path('Models/Media.php'),
        ], 'media-selector-model');

        $timestamp = date('Y_m_d_His');
        $this->publishes([
            __DIR__.'/../database/migrations/2025_10_30_000000_create_media_selector_media_table.php' => database_path("migrations/{$timestamp}_create_media_selector_media_table.php"),
            __DIR__.'/../database/migrations/2025_10_30_000001_create_media_selector_mediables_table.php' => database_path("migrations/{$timestamp}_create_media_selector_mediables_table.php"),
        ], 'media-selector-migrations');

        $this->publishes([
            __DIR__.'/../dist/media-selector.css' => public_path('vendor/media-selector/media-selector.css'),
        ], 'media-selector-assets');

        // Blade directives for including styles
        Blade::directive('mediaSelectorStyles', function () {
            return <<<'BLADE'
<?php if ((bool) config('media-selector.include_builtin_css', true)) : ?>
<link rel="stylesheet" href="<?= asset('vendor/media-selector/media-selector.css') ?>">
<?php endif; ?>
BLADE;
        });

        Blade::directive('mediaSelectorStylesInline', function () {
            return <<<'BLADE'
<?php
$__lmsCssPath = public_path('vendor/media-selector/media-selector.css');
if (is_file($__lmsCssPath)) :
    $__lmsCss = file_get_contents($__lmsCssPath);
?>
<style><?= $__lmsCss ?></style>
<?php endif; ?>
BLADE;
        });
    }
}
