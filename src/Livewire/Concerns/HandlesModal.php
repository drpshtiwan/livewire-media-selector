<?php

namespace DrPshtiwan\LivewireMediaSelector\Livewire\Concerns;

trait HandlesModal
{
    public function updatingSearch(): void
    {
        $this->resetPage('lmsPage');
    }

    public function updatingSort(): void
    {
        $this->resetPage('lmsPage');
    }

    public function updatingSelectedOnly(): void
    {
        $this->resetPage('lmsPage');
    }

    public function openModal(): void
    {
        $this->showModal = true;
        $this->activeTab = 'browse';
        $this->selectedOnly = false;
        $this->search = '';
        // Seed selection from bound value once on open; after that, selection is independent
        if (method_exists($this, 'setSelectedIdsFromValue')) {
            $this->setSelectedIdsFromValue();
        }
        $this->resetPage('lmsPage');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function changeTab(string $tab): void
    {
        $allowed = ['browse', 'upload', 'trash'];
        if (! in_array($tab, $allowed, true)) {
            return;
        }
        $this->activeTab = $tab;
        if ($tab === 'browse') {
            $this->selectedOnly = false;
        }
        $this->resetPage('lmsPage');
    }
}
