<?php

namespace DrPshtiwan\LivewireMediaSelector\Livewire\Concerns;

trait ManagesFilters
{
    private function buildAllowedTokensForDisplay(): array
    {
        if ($this->hasProvidedMimes && count($this->allowedMimes) > 0) {
            return array_values(array_unique(array_filter($this->allowedMimes, fn ($v) => (string) $v !== '')));
        }

        $tokens = [];
        foreach ($this->allowedExtensions as $ext) {
            if (! empty($ext)) {
                $tokens[] = strtoupper(ltrim($ext, '.'));
            }
        }

        return array_values(array_unique($tokens));
    }

    public function updatedMimes($value): void
    {
        $normalize = function ($raw): array {
            if (is_array($raw)) {
                return array_values(array_filter(array_map('trim', $raw), fn ($v) => is_string($v) && $v !== ''));
            }
            if (is_string($raw)) {
                $str = trim($raw);
                if ($str === '') {
                    return [];
                }
                if ((str_starts_with($str, '[') && str_ends_with($str, ']')) || (str_starts_with($str, '"[') && str_ends_with($str, ']"'))) {
                    $decoded = json_decode($str, true);
                    if (is_array($decoded)) {
                        return array_values(array_filter(array_map('trim', $decoded), fn ($v) => is_string($v) && $v !== ''));
                    }
                }

                return array_values(array_filter(array_map('trim', explode(',', $str)), fn ($v) => is_string($v) && $v !== ''));
            }

            return [];
        };

        $this->allowedMimes = array_values(array_unique($normalize($value)));
        $this->hasProvidedMimes = count($this->allowedMimes) > 0;

        $acceptParts = [];
        if ($this->hasProvidedMimes && count($this->allowedMimes)) {
            foreach ($this->allowedMimes as $mt) {
                if (! empty($mt)) {
                    $acceptParts[] = $mt;
                }
            }
        } else {
            foreach ($this->allowedMimes as $mt) {
                if (! empty($mt)) {
                    $acceptParts[] = $mt;
                }
            }
            foreach ($this->allowedExtensions as $ext) {
                if (! empty($ext)) {
                    $acceptParts[] = '.'.ltrim($ext, '.');
                }
            }
        }
        $this->accept = implode(',', array_values(array_unique($acceptParts)));

        $this->resetPage('lmsPage');
    }

    public function updatedExtensions($value): void
    {
        $normalize = function ($raw): array {
            if (is_array($raw)) {
                return array_values(array_filter(array_map('trim', $raw), fn ($v) => is_string($v) && $v !== ''));
            }
            if (is_string($raw)) {
                $str = trim($raw);
                if ($str === '') {
                    return [];
                }
                if ((str_starts_with($str, '[') && str_ends_with($str, ']')) || (str_starts_with($str, '"[') && str_ends_with($str, ']"'))) {
                    $decoded = json_decode($str, true);
                    if (is_array($decoded)) {
                        return array_values(array_filter(array_map('trim', $decoded), fn ($v) => is_string($v) && $v !== ''));
                    }
                }

                return array_values(array_filter(array_map('trim', explode(',', $str)), fn ($v) => is_string($v) && $v !== ''));
            }

            return [];
        };

        $this->allowedExtensions = array_values(array_unique($normalize($value)));

        $acceptParts = [];
        if ($this->hasProvidedMimes && count($this->allowedMimes)) {
            foreach ($this->allowedMimes as $mt) {
                if (! empty($mt)) {
                    $acceptParts[] = $mt;
                }
            }
        } else {
            foreach ($this->allowedMimes as $mt) {
                if (! empty($mt)) {
                    $acceptParts[] = $mt;
                }
            }
            foreach ($this->allowedExtensions as $ext) {
                if (! empty($ext)) {
                    $acceptParts[] = '.'.ltrim($ext, '.');
                }
            }
        }
        $this->accept = implode(',', array_values(array_unique($acceptParts)));
    }
}
