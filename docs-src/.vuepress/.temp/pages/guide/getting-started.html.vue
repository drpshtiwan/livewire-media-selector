<template><div><h1 id="getting-started" tabindex="-1"><a class="header-anchor" href="#getting-started"><span>Getting Started</span></a></h1>
<p>This guide walks you through installing the package, publishing assets, and rendering the Livewire media selector in your application.</p>
<h2 id="requirements" tabindex="-1"><a class="header-anchor" href="#requirements"><span>Requirements</span></a></h2>
<ul>
<li>PHP &gt;= 8.2 (PHP &gt;= 8.3 required for Laravel 13)</li>
<li>Laravel 11, 12, or 13</li>
<li>Livewire 3.5+ or 4.x</li>
</ul>
<p>For Laravel 10, use the <code v-pre>1.x</code> line of this package.</p>
<h2 id="installation" tabindex="-1"><a class="header-anchor" href="#installation"><span>Installation</span></a></h2>
<div class="language-bash line-numbers-mode" data-highlighter="prismjs" data-ext="sh"><pre v-pre><code class="language-bash"><span class="line"><span class="token function">composer</span> require drpshtiwan/livewire-media-selector</span>
<span class="line"></span></code></pre>
<div class="line-numbers" aria-hidden="true" style="counter-reset:line-number 0"><div class="line-number"></div></div></div><p>Publish the default configuration and migrations if you need to customize them:</p>
<div class="language-bash line-numbers-mode" data-highlighter="prismjs" data-ext="sh"><pre v-pre><code class="language-bash"><span class="line">php artisan vendor:publish <span class="token parameter variable">--tag</span><span class="token operator">=</span>media-selector-config</span>
<span class="line">php artisan vendor:publish <span class="token parameter variable">--tag</span><span class="token operator">=</span>media-selector-migrations</span>
<span class="line">php artisan migrate</span>
<span class="line"></span></code></pre>
<div class="line-numbers" aria-hidden="true" style="counter-reset:line-number 0"><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div></div></div><p>Publish the UI assets (Tailwind build) so the stylesheet directive can serve them:</p>
<div class="language-bash line-numbers-mode" data-highlighter="prismjs" data-ext="sh"><pre v-pre><code class="language-bash"><span class="line">php artisan vendor:publish <span class="token parameter variable">--tag</span><span class="token operator">=</span>media-selector-assets <span class="token parameter variable">--force</span></span>
<span class="line"></span></code></pre>
<div class="line-numbers" aria-hidden="true" style="counter-reset:line-number 0"><div class="line-number"></div></div></div><p>Add the directive to a shared layout (after <code v-pre>@livewireStyles</code>):</p>
<div class="language-blade line-numbers-mode" data-highlighter="prismjs" data-ext="blade"><pre v-pre><code class="language-blade"><span class="line">&lt;!-- resources/views/layouts/app.blade.php --&gt;</span>
<span class="line">&lt;head&gt;</span>
<span class="line">    &lt;!-- ... --&gt;</span>
<span class="line">    @livewireStyles</span>
<span class="line">    @mediaSelectorStyles</span>
<span class="line">&lt;/head&gt;</span>
<span class="line"></span></code></pre>
<div class="line-numbers" aria-hidden="true" style="counter-reset:line-number 0"><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div></div></div><h2 id="registering-the-trait" tabindex="-1"><a class="header-anchor" href="#registering-the-trait"><span>Registering the trait</span></a></h2>
<p>Attach media to any Eloquent model via the provided trait:</p>
<div class="language-php line-numbers-mode" data-highlighter="prismjs" data-ext="php"><pre v-pre><code class="language-php"><span class="line"><span class="token keyword">use</span> <span class="token package">DrPshtiwan<span class="token punctuation">\</span>LivewireMediaSelector<span class="token punctuation">\</span>Concerns<span class="token punctuation">\</span>HasMediaSelector</span><span class="token punctuation">;</span></span>
<span class="line"></span>
<span class="line"><span class="token keyword">class</span> <span class="token class-name-definition class-name">Post</span> <span class="token keyword">extends</span> <span class="token class-name">Model</span></span>
<span class="line"><span class="token punctuation">{</span></span>
<span class="line">    <span class="token keyword">use</span> <span class="token package">HasMediaSelector</span><span class="token punctuation">;</span></span>
<span class="line"><span class="token punctuation">}</span></span>
<span class="line"></span></code></pre>
<div class="line-numbers" aria-hidden="true" style="counter-reset:line-number 0"><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div></div></div><p>The trait exposes helper methods such as <code v-pre>attachMedia</code>, <code v-pre>syncMedia</code>, <code v-pre>getMedia</code>, <code v-pre>getMediaUrls</code>, and <code v-pre>getMediaUrl</code>.</p>
<h2 id="rendering-the-livewire-component" tabindex="-1"><a class="header-anchor" href="#rendering-the-livewire-component"><span>Rendering the Livewire component</span></a></h2>
<div class="language-blade line-numbers-mode" data-highlighter="prismjs" data-ext="blade"><pre v-pre><code class="language-blade"><span class="line">&lt;livewire:media-selector</span>
<span class="line">    wire:model=&quot;media&quot;</span>
<span class="line">    :multiple=&quot;true&quot;</span>
<span class="line">    collection=&quot;gallery&quot;</span>
<span class="line">    :can-upload=&quot;true&quot;</span>
<span class="line">    :can-delete=&quot;false&quot;</span>
<span class="line">/&gt;</span>
<span class="line"></span></code></pre>
<div class="line-numbers" aria-hidden="true" style="counter-reset:line-number 0"><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div></div></div><h3 id="stylesheet-directive" tabindex="-1"><a class="header-anchor" href="#stylesheet-directive"><span>Stylesheet directive</span></a></h3>
<p>Include the packaged styles in your layout (after <code v-pre>@livewireStyles</code>):</p>
<div class="language-blade line-numbers-mode" data-highlighter="prismjs" data-ext="blade"><pre v-pre><code class="language-blade"><span class="line">@livewireStyles</span>
<span class="line">@mediaSelectorStyles</span>
<span class="line"></span></code></pre>
<div class="line-numbers" aria-hidden="true" style="counter-reset:line-number 0"><div class="line-number"></div><div class="line-number"></div></div></div><p>If you publish the CSS to customize it, keep the directive in place (it will point to your published file).</p>
<h3 id="opening-the-modal-programmatically" tabindex="-1"><a class="header-anchor" href="#opening-the-modal-programmatically"><span>Opening the modal programmatically</span></a></h3>
<div class="language-php line-numbers-mode" data-highlighter="prismjs" data-ext="php"><pre v-pre><code class="language-php"><span class="line"><span class="token class-name static-context">Livewire</span><span class="token operator">::</span><span class="token function">test</span><span class="token punctuation">(</span><span class="token class-name class-name-fully-qualified static-context"><span class="token punctuation">\</span>DrPshtiwan<span class="token punctuation">\</span>LivewireMediaSelector<span class="token punctuation">\</span>Livewire<span class="token punctuation">\</span>MediaSelector</span><span class="token operator">::</span><span class="token keyword">class</span><span class="token punctuation">)</span></span>
<span class="line">    <span class="token operator">-></span><span class="token function">call</span><span class="token punctuation">(</span><span class="token string single-quoted-string">'openModal'</span><span class="token punctuation">)</span><span class="token punctuation">;</span></span>
<span class="line"></span></code></pre>
<div class="line-numbers" aria-hidden="true" style="counter-reset:line-number 0"><div class="line-number"></div><div class="line-number"></div></div></div><h3 id="receiving-selection-data" tabindex="-1"><a class="header-anchor" href="#receiving-selection-data"><span>Receiving selection data</span></a></h3>
<p>When the user confirms their selection, the component updates <code v-pre>wire:model</code> with an array of items shaped like:</p>
<div class="language-json line-numbers-mode" data-highlighter="prismjs" data-ext="json"><pre v-pre><code class="language-json"><span class="line"><span class="token punctuation">[</span></span>
<span class="line">  <span class="token punctuation">{</span> <span class="token property">"id"</span><span class="token operator">:</span> <span class="token number">15</span><span class="token punctuation">,</span> <span class="token property">"collection"</span><span class="token operator">:</span> <span class="token string">"gallery"</span><span class="token punctuation">,</span> <span class="token property">"path"</span><span class="token operator">:</span> <span class="token string">"media/gallery/hero.jpg"</span> <span class="token punctuation">}</span></span>
<span class="line"><span class="token punctuation">]</span></span>
<span class="line"></span></code></pre>
<div class="line-numbers" aria-hidden="true" style="counter-reset:line-number 0"><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div></div></div><p>You can store the payload or call <code v-pre>$post-&gt;syncMedia($payload, 'gallery');</code> to persist the relation.</p>
</div></template>


