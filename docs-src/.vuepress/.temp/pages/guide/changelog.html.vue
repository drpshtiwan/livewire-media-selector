<template><div><h1 id="changelog" tabindex="-1"><a class="header-anchor" href="#changelog"><span>Changelog</span></a></h1>
<p>This page summarizes recent package updates.</p>
<h2 id="unreleased" tabindex="-1"><a class="header-anchor" href="#unreleased"><span>Unreleased</span></a></h2>
<h3 id="added" tabindex="-1"><a class="header-anchor" href="#added"><span>Added</span></a></h3>
<ul>
<li>Support for <strong>Laravel 11, 12, and 13</strong> (dropped Laravel 10).</li>
<li><code v-pre>show_thumbnails</code> config option and <code v-pre>show-thumbnails</code> component attribute.</li>
</ul>
<h3 id="security" tabindex="-1"><a class="header-anchor" href="#security"><span>Security</span></a></h3>
<ul>
<li>Permission and storage/validation properties are now <code v-pre>#[Locked]</code> and resolved server-side only, so the browser can no longer flip permissions, bypass upload-type validation, or repoint storage.</li>
<li><code v-pre>svg</code> removed from default <code v-pre>allowed_extensions</code> (stored-XSS risk when served from a public disk).</li>
</ul>
<h3 id="compatibility" tabindex="-1"><a class="header-anchor" href="#compatibility"><span>Compatibility</span></a></h3>
<ul>
<li>Minimum PHP raised to 8.2 (8.3 for Laravel 13); minimum Livewire raised to 3.5.</li>
<li>CI now tests PHP 8.2–8.4 across Laravel 11–13.</li>
</ul>
<h3 id="fixed" tabindex="-1"><a class="header-anchor" href="#fixed"><span>Fixed</span></a></h3>
<ul>
<li><code v-pre>show_thumbnails=false</code> disables image rendering only in selected previews below Clear/Choose Media; modal grids still render thumbnails.</li>
</ul>
<h3 id="changed" tabindex="-1"><a class="header-anchor" href="#changed"><span>Changed</span></a></h3>
<ul>
<li>Action methods now enforce scoped media access (disk/user/collection/extra scope).</li>
<li>Per-instance overrides now support <code v-pre>disk</code>, <code v-pre>directory</code>, <code v-pre>per-page</code>, and <code v-pre>max-upload-kb</code>.</li>
<li>Media model and trait wiring now respect configured <code v-pre>table</code> and <code v-pre>model</code>.</li>
<li>Preview handling is safer for missing files via guarded MIME/URL lookup.</li>
<li>Trash pagination reset is consistent with <code v-pre>lmsPage</code>.</li>
<li>Documentation now reflects Tailwind-only UI support.</li>
<li><code v-pre>show_thumbnails=false</code> now skips rendering the selected-preview section below Clear/Choose Media.</li>
<li>Livewire 4 compatibility remains supported, including event dispatch behavior coverage.</li>
</ul>
<h3 id="docs" tabindex="-1"><a class="header-anchor" href="#docs"><span>Docs</span></a></h3>
<ul>
<li>Install command corrected to <code v-pre>composer require drpshtiwan/livewire-media-selector</code>.</li>
<li>Attribute docs aligned with current <code v-pre>wire:model</code> payload behavior.</li>
</ul>
<h3 id="planned-features" tabindex="-1"><a class="header-anchor" href="#planned-features"><span>Planned Features</span></a></h3>
<ul>
<li>Authorization policy integration (Gate/Policy hooks per action/item).</li>
<li>Pluggable UI templates/slots for media card rendering.</li>
<li>Advanced uploads (chunked, resumable, direct-to-cloud options).</li>
<li>Media editing tools (crop/rotate/resize before save).</li>
<li>Rich organization (folders, tags, favorites, metadata filters).</li>
<li>Accessibility upgrades (focus trap and stronger keyboard navigation).</li>
<li>Bulk actions (move, download, metadata edit, select-all workflows).</li>
<li>Optional strict single-select payload mode (single object output).</li>
</ul>
<p>For full details, see the root package changelog: <RouteLink to="/CHANGELOG.html"><code v-pre>CHANGELOG.md</code></RouteLink>.</p>
</div></template>


