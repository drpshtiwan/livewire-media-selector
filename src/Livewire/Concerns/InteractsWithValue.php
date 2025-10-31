<?php

namespace DrPshtiwan\LivewireMediaSelector\Livewire\Concerns;

trait InteractsWithValue
{
    private function normalizeValueForPreview(callable $normalize): void
    {
        if ($this->multiple) {
            if (is_string($this->value)) {
                $this->value = $normalize($this->value);
            } elseif (! is_array($this->value)) {
                $this->value = [];
            }
        } else {
            if (is_array($this->value)) {
                $first = $this->value[0] ?? null;
                if (is_string($first)) {
                    $this->value = $first;
                } else {
                    $this->value = array_values($this->value);
                }
            }
        }
    }

    private function setSelectedIdsFromValue(): void
    {
        if (is_array($this->value)) {
            $ids = [];
            foreach ($this->value as $entry) {
                if (is_array($entry) && isset($entry['id']) && is_numeric($entry['id'])) {
                    $ids[] = (int) $entry['id'];
                }
            }
            if (count($ids)) {
                $this->selectedIds = array_values(array_unique($ids));
            }
        } elseif (is_string($this->value)) {
            // Intentionally no-op: cannot infer id from path string
        }
    }
}
