<template><div><h1 id="attribute-reference" tabindex="-1"><a class="header-anchor" href="#attribute-reference"><span>Attribute Reference</span></a></h1>
<p>Pass these attributes directly to the <code v-pre>&lt;livewire:media-selector /&gt;</code> component to override configuration values on a per-instance basis.</p>
<table>
<thead>
<tr>
<th>Attribute</th>
<th>Type</th>
<th>Default</th>
<th>Description</th>
</tr>
</thead>
<tbody>
<tr>
<td><code v-pre>wire:model</code> / <code v-pre>wire:model.defer</code></td>
<td>`array</td>
<td>string</td>
<td>null`</td>
</tr>
<tr>
<td><code v-pre>collection</code></td>
<td>`string</td>
<td>null`</td>
<td><code v-pre>null</code></td>
</tr>
<tr>
<td><code v-pre>multiple</code></td>
<td><code v-pre>bool</code></td>
<td><code v-pre>config('media-selector.multiple', false)</code></td>
<td>Enables multi-select mode. When <code v-pre>false</code>, the selector behaves like a single-value input.</td>
</tr>
<tr>
<td><code v-pre>can-upload</code> / <code v-pre>:can-upload</code></td>
<td><code v-pre>bool</code></td>
<td><code v-pre>config('media-selector.can_upload', true)</code></td>
<td>Toggles the Upload tab and the ability to upload new files.</td>
</tr>
<tr>
<td><code v-pre>can-delete</code> / <code v-pre>:can-delete</code></td>
<td><code v-pre>bool</code></td>
<td><code v-pre>config('media-selector.can_delete', false)</code></td>
<td>Shows delete buttons and enables soft-deleting media items.</td>
</tr>
<tr>
<td><code v-pre>can-see-trash</code> / <code v-pre>:can-see-trash</code></td>
<td><code v-pre>bool</code></td>
<td><code v-pre>config('media-selector.can_see_trash', false)</code></td>
<td>Exposes the Trash tab with soft-deleted media.</td>
</tr>
<tr>
<td><code v-pre>can-restore-trash</code> / <code v-pre>:can-restore-trash</code></td>
<td><code v-pre>bool</code></td>
<td><code v-pre>config('media-selector.can_restore_trash', false)</code></td>
<td>Allows restoring items from the Trash tab.</td>
</tr>
<tr>
<td><code v-pre>restrict-to-current-user</code> / <code v-pre>:restrict-to-current-user</code></td>
<td><code v-pre>bool</code></td>
<td><code v-pre>config('media-selector.restrict_to_current_user', false)</code></td>
<td>Limits the query to records owned by the authenticated user (<code v-pre>user_id = Auth::id()</code>).</td>
</tr>
<tr>
<td><code v-pre>extensions</code> / <code v-pre>:extensions</code></td>
<td>`array</td>
<td>string`</td>
<td><code v-pre>config('media-selector.allowed_extensions', [...])</code></td>
</tr>
<tr>
<td><code v-pre>mimes</code> / <code v-pre>:mimes</code></td>
<td>`array</td>
<td>string`</td>
<td><code v-pre>config('media-selector.allowed_mimes', [])</code></td>
</tr>
<tr>
<td><code v-pre>ui</code></td>
<td><code v-pre>string</code></td>
<td><code v-pre>config('media-selector.ui', 'tailwind')</code></td>
<td>UI variant. Currently supports <code v-pre>tailwind</code>.</td>
</tr>
<tr>
<td><code v-pre>disk</code></td>
<td><code v-pre>string</code></td>
<td><code v-pre>config('media-selector.disk', 'public')</code></td>
<td>Sets the filesystem disk used for uploads and URL generation.</td>
</tr>
<tr>
<td><code v-pre>directory</code></td>
<td><code v-pre>string</code></td>
<td><code v-pre>config('media-selector.directory', 'media')</code></td>
<td>Base directory on the chosen disk where uploads are stored.</td>
</tr>
<tr>
<td><code v-pre>per-page</code> / <code v-pre>:per-page</code></td>
<td><code v-pre>int</code></td>
<td><code v-pre>config('media-selector.per_page', 24)</code></td>
<td>Number of media items per page within the modal.</td>
</tr>
<tr>
<td><code v-pre>max-upload-kb</code> / <code v-pre>:max-upload-kb</code></td>
<td><code v-pre>int</code></td>
<td><code v-pre>config('media-selector.max_upload_kb', 5120)</code></td>
<td>Maximum upload size in kilobytes.</td>
</tr>
<tr>
<td><code v-pre>show-thumbnails</code> / <code v-pre>:show-thumbnails</code></td>
<td><code v-pre>bool</code></td>
<td><code v-pre>config('media-selector.show_thumbnails', true)</code></td>
<td>Shows or hides rendered image thumbnails in the selected preview area below Clear/Choose Media. When <code v-pre>false</code>, that preview area is not rendered; modal grids are unaffected.</td>
</tr>
<tr>
<td><code v-pre>require-width</code> / <code v-pre>:require-width</code></td>
<td>`int</td>
<td>null`</td>
<td><code v-pre>null</code></td>
</tr>
<tr>
<td><code v-pre>require-height</code> / <code v-pre>:require-height</code></td>
<td>`int</td>
<td>null`</td>
<td><code v-pre>null</code></td>
</tr>
<tr>
<td><code v-pre>require-aspect-ratio</code> / <code v-pre>:require-aspect-ratio</code></td>
<td>`string</td>
<td>null`</td>
<td><code v-pre>null</code></td>
</tr>
<tr>
<td><code v-pre>value</code></td>
<td>`array</td>
<td>string</td>
<td>null`</td>
</tr>
<tr>
<td><code v-pre>selected-only</code> / <code v-pre>:selected-only</code></td>
<td><code v-pre>bool</code></td>
<td><code v-pre>false</code></td>
<td>Forces the grid to display only the currently selected items. Useful for read-only previews.</td>
</tr>
<tr>
<td><code v-pre>sort</code></td>
<td><code v-pre>string</code></td>
<td><code v-pre>'newest'</code></td>
<td>Controls initial sort order. Supported values: <code v-pre>newest</code>, <code v-pre>oldest</code>, <code v-pre>name_asc</code>, <code v-pre>name_desc</code>.</td>
</tr>
<tr>
<td><code v-pre>search</code></td>
<td><code v-pre>string</code></td>
<td><code v-pre>''</code></td>
<td>Sets the initial search term for the media list.</td>
</tr>
<tr>
<td><code v-pre>show-modal</code> / <code v-pre>:show-modal</code></td>
<td><code v-pre>bool</code></td>
<td><code v-pre>false</code></td>
<td>Manually toggles the modal open/closed state. Usually managed internally via component methods.</td>
</tr>
<tr>
<td><code v-pre>selected-ids</code> / <code v-pre>:selected-ids</code></td>
<td><code v-pre>array&lt;int&gt;</code></td>
<td><code v-pre>[]</code></td>
<td>Pre-selects specific media IDs in the grid. Primarily used internally when syncing with the bound value.</td>
</tr>
</tbody>
</table>
<div class="hint-container tip">
<p class="hint-container-title">Attribute casing</p>
<p>When using Blade/Livewire, kebab-case attribute names (e.g. <code v-pre>can-upload</code>) map to the camelCase properties on the component.</p>
</div>
<h3 id="query-helper" tabindex="-1"><a class="header-anchor" href="#query-helper"><span>Query helper</span></a></h3>
<p>The trait includes an Eloquent scope for eagerly loading media:</p>
<div class="language-php line-numbers-mode" data-highlighter="prismjs" data-ext="php"><pre v-pre><code class="language-php"><span class="line"><span class="token variable">$posts</span> <span class="token operator">=</span> <span class="token class-name static-context">Post</span><span class="token operator">::</span><span class="token function">query</span><span class="token punctuation">(</span><span class="token punctuation">)</span></span>
<span class="line">    <span class="token operator">-></span><span class="token function">withMediaCollection</span><span class="token punctuation">(</span><span class="token string single-quoted-string">'gallery'</span><span class="token punctuation">,</span> <span class="token keyword">function</span> <span class="token punctuation">(</span><span class="token variable">$relation</span><span class="token punctuation">)</span> <span class="token punctuation">{</span></span>
<span class="line">        <span class="token variable">$relation</span><span class="token operator">-></span><span class="token function">orderBy</span><span class="token punctuation">(</span><span class="token string single-quoted-string">'media.id'</span><span class="token punctuation">)</span><span class="token punctuation">;</span></span>
<span class="line">    <span class="token punctuation">}</span><span class="token punctuation">)</span></span>
<span class="line">    <span class="token operator">-></span><span class="token function">get</span><span class="token punctuation">(</span><span class="token punctuation">)</span><span class="token punctuation">;</span></span>
<span class="line"></span></code></pre>
<div class="line-numbers" aria-hidden="true" style="counter-reset:line-number 0"><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div></div></div><p>This scope loads the <code v-pre>media</code> relation (optionally filtered by collection) so repeated calls to <code v-pre>getMedia('gallery')</code> do not perform extra queries.</p>
<h2 id="examples" tabindex="-1"><a class="header-anchor" href="#examples"><span>Examples</span></a></h2>
<h3 id="basic-single-select" tabindex="-1"><a class="header-anchor" href="#basic-single-select"><span>Basic single-select</span></a></h3>
<div class="language-blade line-numbers-mode" data-highlighter="prismjs" data-ext="blade"><pre v-pre><code class="language-blade"><span class="line">&lt;livewire:media-selector wire:model=&quot;heroImage&quot; /&gt;</span>
<span class="line"></span></code></pre>
<div class="line-numbers" aria-hidden="true" style="counter-reset:line-number 0"><div class="line-number"></div></div></div><div class="language-php line-numbers-mode" data-highlighter="prismjs" data-ext="php"><pre v-pre><code class="language-php"><span class="line"><span class="token keyword">public</span> <span class="token keyword type-declaration">array</span><span class="token operator">|</span><span class="token keyword type-declaration">string</span><span class="token operator">|</span><span class="token keyword type-declaration">null</span> <span class="token variable">$heroImage</span> <span class="token operator">=</span> <span class="token constant">null</span><span class="token punctuation">;</span></span>
<span class="line"></span>
<span class="line"><span class="token keyword">public</span> <span class="token keyword">function</span> <span class="token function-definition function">save</span><span class="token punctuation">(</span><span class="token punctuation">)</span><span class="token punctuation">:</span> <span class="token keyword return-type">void</span></span>
<span class="line"><span class="token punctuation">{</span></span>
<span class="line">    <span class="token variable">$this</span><span class="token operator">-></span><span class="token property">post</span><span class="token operator">-></span><span class="token function">syncMedia</span><span class="token punctuation">(</span><span class="token variable">$this</span><span class="token operator">-></span><span class="token property">heroImage</span><span class="token punctuation">,</span> <span class="token string single-quoted-string">'hero'</span><span class="token punctuation">)</span><span class="token punctuation">;</span></span>
<span class="line"><span class="token punctuation">}</span></span>
<span class="line"></span></code></pre>
<div class="line-numbers" aria-hidden="true" style="counter-reset:line-number 0"><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div></div></div><h3 id="multi-select-gallery-with-collection-ordering" tabindex="-1"><a class="header-anchor" href="#multi-select-gallery-with-collection-ordering"><span>Multi-select gallery with collection &amp; ordering</span></a></h3>
<div class="language-blade line-numbers-mode" data-highlighter="prismjs" data-ext="blade"><pre v-pre><code class="language-blade"><span class="line">&lt;livewire:media-selector</span>
<span class="line">    wire:model=&quot;gallery&quot;</span>
<span class="line">    collection=&quot;gallery&quot;</span>
<span class="line">    :multiple=&quot;true&quot;</span>
<span class="line">    :can-upload=&quot;true&quot;</span>
<span class="line">    :can-delete=&quot;true&quot;</span>
<span class="line">/&gt;</span>
<span class="line"></span></code></pre>
<div class="line-numbers" aria-hidden="true" style="counter-reset:line-number 0"><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div></div></div><div class="language-php line-numbers-mode" data-highlighter="prismjs" data-ext="php"><pre v-pre><code class="language-php"><span class="line"><span class="token keyword">public</span> <span class="token keyword type-declaration">array</span> <span class="token variable">$gallery</span> <span class="token operator">=</span> <span class="token punctuation">[</span><span class="token punctuation">]</span><span class="token punctuation">;</span></span>
<span class="line"></span>
<span class="line"><span class="token keyword">public</span> <span class="token keyword">function</span> <span class="token function-definition function">mount</span><span class="token punctuation">(</span><span class="token class-name type-declaration">Post</span> <span class="token variable">$post</span><span class="token punctuation">)</span><span class="token punctuation">:</span> <span class="token keyword return-type">void</span></span>
<span class="line"><span class="token punctuation">{</span></span>
<span class="line">    <span class="token variable">$this</span><span class="token operator">-></span><span class="token property">gallery</span> <span class="token operator">=</span> <span class="token variable">$post</span><span class="token operator">-></span><span class="token function">getMediaPayload</span><span class="token punctuation">(</span><span class="token string single-quoted-string">'gallery'</span><span class="token punctuation">)</span><span class="token punctuation">;</span></span>
<span class="line">    <span class="token variable">$this</span><span class="token operator">-></span><span class="token property">post</span> <span class="token operator">=</span> <span class="token class-name static-context">Post</span><span class="token operator">::</span><span class="token function">query</span><span class="token punctuation">(</span><span class="token punctuation">)</span></span>
<span class="line">        <span class="token operator">-></span><span class="token function">withMediaCollection</span><span class="token punctuation">(</span><span class="token string single-quoted-string">'gallery'</span><span class="token punctuation">)</span></span>
<span class="line">        <span class="token operator">-></span><span class="token function">findOrFail</span><span class="token punctuation">(</span><span class="token variable">$post</span><span class="token operator">-></span><span class="token function">getKey</span><span class="token punctuation">(</span><span class="token punctuation">)</span><span class="token punctuation">)</span><span class="token punctuation">;</span></span>
<span class="line"><span class="token punctuation">}</span></span>
<span class="line"></span>
<span class="line"><span class="token keyword">public</span> <span class="token keyword">function</span> <span class="token function-definition function">save</span><span class="token punctuation">(</span><span class="token punctuation">)</span><span class="token punctuation">:</span> <span class="token keyword return-type">void</span></span>
<span class="line"><span class="token punctuation">{</span></span>
<span class="line">    <span class="token variable">$this</span><span class="token operator">-></span><span class="token property">post</span><span class="token operator">-></span><span class="token function">syncMedia</span><span class="token punctuation">(</span><span class="token variable">$this</span><span class="token operator">-></span><span class="token property">gallery</span><span class="token punctuation">,</span> <span class="token string single-quoted-string">'gallery'</span><span class="token punctuation">)</span><span class="token punctuation">;</span></span>
<span class="line"><span class="token punctuation">}</span></span>
<span class="line"></span></code></pre>
<div class="line-numbers" aria-hidden="true" style="counter-reset:line-number 0"><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div></div></div><h3 id="tailwind-ui-video-only-uploads" tabindex="-1"><a class="header-anchor" href="#tailwind-ui-video-only-uploads"><span>Tailwind UI, video-only uploads</span></a></h3>
<div class="language-blade line-numbers-mode" data-highlighter="prismjs" data-ext="blade"><pre v-pre><code class="language-blade"><span class="line">&lt;livewire:media-selector</span>
<span class="line">    wire:model=&quot;videos&quot;</span>
<span class="line">    ui=&quot;tailwind&quot;</span>
<span class="line">    mimes='[&quot;video/*&quot;]'</span>
<span class="line">    :multiple=&quot;true&quot;</span>
<span class="line">/&gt;</span>
<span class="line"></span></code></pre>
<div class="line-numbers" aria-hidden="true" style="counter-reset:line-number 0"><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div></div></div><h3 id="user-restricted-library-with-trash-management" tabindex="-1"><a class="header-anchor" href="#user-restricted-library-with-trash-management"><span>User-restricted library with trash management</span></a></h3>
<div class="language-blade line-numbers-mode" data-highlighter="prismjs" data-ext="blade"><pre v-pre><code class="language-blade"><span class="line">&lt;livewire:media-selector</span>
<span class="line">    wire:model=&quot;documents&quot;</span>
<span class="line">    collection=&quot;docs&quot;</span>
<span class="line">    :restrict-to-current-user=&quot;true&quot;</span>
<span class="line">    :can-see-trash=&quot;true&quot;</span>
<span class="line">    :can-restore-trash=&quot;true&quot;</span>
<span class="line">/&gt;</span>
<span class="line"></span></code></pre>
<div class="line-numbers" aria-hidden="true" style="counter-reset:line-number 0"><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div></div></div><h3 id="strict-dimension-requirements" tabindex="-1"><a class="header-anchor" href="#strict-dimension-requirements"><span>Strict dimension requirements</span></a></h3>
<div class="language-blade line-numbers-mode" data-highlighter="prismjs" data-ext="blade"><pre v-pre><code class="language-blade"><span class="line">&lt;livewire:media-selector</span>
<span class="line">    wire:model=&quot;banners&quot;</span>
<span class="line">    :multiple=&quot;true&quot;</span>
<span class="line">    :require-width=&quot;1920&quot;</span>
<span class="line">    :require-height=&quot;1080&quot;</span>
<span class="line">    require-aspect-ratio=&quot;16:9&quot;</span>
<span class="line">/&gt;</span>
<span class="line"></span></code></pre>
<div class="line-numbers" aria-hidden="true" style="counter-reset:line-number 0"><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div></div></div><p>Each example can be combined—attributes layer on top of configuration defaults so you can tailor the selector per use-case.</p>
<h3 id="product-model-example-thumbnail-gallery" tabindex="-1"><a class="header-anchor" href="#product-model-example-thumbnail-gallery"><span>Product model example (thumbnail + gallery)</span></a></h3>
<p>Assuming your <code v-pre>Product</code> model uses the <code v-pre>HasMediaSelector</code> trait:</p>
<div class="language-php line-numbers-mode" data-highlighter="prismjs" data-ext="php"><pre v-pre><code class="language-php"><span class="line"><span class="token keyword">class</span> <span class="token class-name-definition class-name">Product</span> <span class="token keyword">extends</span> <span class="token class-name">Model</span></span>
<span class="line"><span class="token punctuation">{</span></span>
<span class="line">    <span class="token keyword">use</span> <span class="token package">HasMediaSelector</span><span class="token punctuation">;</span></span>
<span class="line"><span class="token punctuation">}</span></span>
<span class="line"></span></code></pre>
<div class="line-numbers" aria-hidden="true" style="counter-reset:line-number 0"><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div></div></div><p>In your Livewire component:</p>
<div class="language-php line-numbers-mode" data-highlighter="prismjs" data-ext="php"><pre v-pre><code class="language-php"><span class="line"><span class="token keyword">class</span> <span class="token class-name-definition class-name">EditProduct</span> <span class="token keyword">extends</span> <span class="token class-name">Component</span></span>
<span class="line"><span class="token punctuation">{</span></span>
<span class="line">    <span class="token keyword">public</span> <span class="token class-name type-declaration">Product</span> <span class="token variable">$product</span><span class="token punctuation">;</span></span>
<span class="line">    <span class="token keyword">public</span> <span class="token keyword type-declaration">array</span><span class="token operator">|</span><span class="token keyword type-declaration">null</span> <span class="token variable">$thumbnail</span> <span class="token operator">=</span> <span class="token constant">null</span><span class="token punctuation">;</span></span>
<span class="line">    <span class="token keyword">public</span> <span class="token keyword type-declaration">array</span> <span class="token variable">$gallery</span> <span class="token operator">=</span> <span class="token punctuation">[</span><span class="token punctuation">]</span><span class="token punctuation">;</span></span>
<span class="line"></span>
<span class="line">    <span class="token keyword">public</span> <span class="token keyword">function</span> <span class="token function-definition function">mount</span><span class="token punctuation">(</span><span class="token class-name type-declaration">Product</span> <span class="token variable">$product</span><span class="token punctuation">)</span><span class="token punctuation">:</span> <span class="token keyword return-type">void</span></span>
<span class="line">    <span class="token punctuation">{</span></span>
<span class="line">        <span class="token variable">$this</span><span class="token operator">-></span><span class="token property">product</span> <span class="token operator">=</span> <span class="token class-name static-context">Product</span><span class="token operator">::</span><span class="token function">query</span><span class="token punctuation">(</span><span class="token punctuation">)</span></span>
<span class="line">            <span class="token operator">-></span><span class="token function">withMediaCollection</span><span class="token punctuation">(</span><span class="token string single-quoted-string">'gallery'</span><span class="token punctuation">)</span></span>
<span class="line">            <span class="token operator">-></span><span class="token function">findOrFail</span><span class="token punctuation">(</span><span class="token variable">$product</span><span class="token operator">-></span><span class="token function">getKey</span><span class="token punctuation">(</span><span class="token punctuation">)</span><span class="token punctuation">)</span><span class="token punctuation">;</span></span>
<span class="line"></span>
<span class="line">        <span class="token variable">$this</span><span class="token operator">-></span><span class="token property">thumbnail</span> <span class="token operator">=</span> <span class="token variable">$product</span><span class="token operator">-></span><span class="token function">getMediaPayload</span><span class="token punctuation">(</span><span class="token string single-quoted-string">'thumbnail'</span><span class="token punctuation">)</span><span class="token punctuation">;</span></span>
<span class="line">        <span class="token variable">$this</span><span class="token operator">-></span><span class="token property">gallery</span> <span class="token operator">=</span> <span class="token variable">$product</span><span class="token operator">-></span><span class="token function">getMediaPayload</span><span class="token punctuation">(</span><span class="token string single-quoted-string">'gallery'</span><span class="token punctuation">)</span><span class="token punctuation">;</span></span>
<span class="line">    <span class="token punctuation">}</span></span>
<span class="line"></span>
<span class="line">    <span class="token keyword">public</span> <span class="token keyword">function</span> <span class="token function-definition function">save</span><span class="token punctuation">(</span><span class="token punctuation">)</span><span class="token punctuation">:</span> <span class="token keyword return-type">void</span></span>
<span class="line">    <span class="token punctuation">{</span></span>
<span class="line">        <span class="token variable">$this</span><span class="token operator">-></span><span class="token property">product</span><span class="token operator">-></span><span class="token function">syncMedia</span><span class="token punctuation">(</span><span class="token variable">$this</span><span class="token operator">-></span><span class="token property">thumbnail</span><span class="token punctuation">,</span> <span class="token string single-quoted-string">'thumbnail'</span><span class="token punctuation">)</span><span class="token punctuation">;</span></span>
<span class="line">        <span class="token variable">$this</span><span class="token operator">-></span><span class="token property">product</span><span class="token operator">-></span><span class="token function">syncMedia</span><span class="token punctuation">(</span><span class="token variable">$this</span><span class="token operator">-></span><span class="token property">gallery</span><span class="token punctuation">,</span> <span class="token string single-quoted-string">'gallery'</span><span class="token punctuation">)</span><span class="token punctuation">;</span></span>
<span class="line"></span>
<span class="line">        <span class="token variable">$this</span><span class="token operator">-></span><span class="token function">dispatch</span><span class="token punctuation">(</span><span class="token string single-quoted-string">'product-saved'</span><span class="token punctuation">)</span><span class="token punctuation">;</span></span>
<span class="line">    <span class="token punctuation">}</span></span>
<span class="line"><span class="token punctuation">}</span></span>
<span class="line"></span></code></pre>
<div class="line-numbers" aria-hidden="true" style="counter-reset:line-number 0"><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div></div></div><p>Blade view:</p>
<div class="language-blade line-numbers-mode" data-highlighter="prismjs" data-ext="blade"><pre v-pre><code class="language-blade"><span class="line">&lt;div class=&quot;space-y-6&quot;&gt;</span>
<span class="line">    &lt;div&gt;</span>
<span class="line">        &lt;h3 class=&quot;font-semibold mb-2&quot;&gt;Thumbnail&lt;/h3&gt;</span>
<span class="line">        &lt;livewire:media-selector</span>
<span class="line">            wire:model=&quot;thumbnail&quot;</span>
<span class="line">            collection=&quot;thumbnail&quot;</span>
<span class="line">            :multiple=&quot;false&quot;</span>
<span class="line">            :require-width=&quot;800&quot;</span>
<span class="line">            :require-height=&quot;600&quot;</span>
<span class="line">        /&gt;</span>
<span class="line">    &lt;/div&gt;</span>
<span class="line"></span>
<span class="line">    &lt;div&gt;</span>
<span class="line">        &lt;h3 class=&quot;font-semibold mb-2&quot;&gt;Gallery&lt;/h3&gt;</span>
<span class="line">        &lt;livewire:media-selector</span>
<span class="line">            wire:model=&quot;gallery&quot;</span>
<span class="line">            collection=&quot;gallery&quot;</span>
<span class="line">            :multiple=&quot;true&quot;</span>
<span class="line">            :can-upload=&quot;true&quot;</span>
<span class="line">            :can-delete=&quot;true&quot;</span>
<span class="line">        /&gt;</span>
<span class="line">    &lt;/div&gt;</span>
<span class="line">&lt;/div&gt;</span>
<span class="line"></span></code></pre>
<div class="line-numbers" aria-hidden="true" style="counter-reset:line-number 0"><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div></div></div><p>Whenever you need to render images on the storefront:</p>
<div class="language-php line-numbers-mode" data-highlighter="prismjs" data-ext="php"><pre v-pre><code class="language-php"><span class="line"><span class="token variable">$thumbUrl</span> <span class="token operator">=</span> <span class="token variable">$product</span><span class="token operator">-></span><span class="token function">getMediaUrl</span><span class="token punctuation">(</span><span class="token string single-quoted-string">'thumbnail'</span><span class="token punctuation">)</span><span class="token punctuation">;</span></span>
<span class="line"><span class="token variable">$galleryUrls</span> <span class="token operator">=</span> <span class="token variable">$product</span><span class="token operator">-></span><span class="token function">getMediaUrls</span><span class="token punctuation">(</span><span class="token string single-quoted-string">'gallery'</span><span class="token punctuation">)</span><span class="token punctuation">;</span></span>
<span class="line"></span></code></pre>
<div class="line-numbers" aria-hidden="true" style="counter-reset:line-number 0"><div class="line-number"></div><div class="line-number"></div></div></div><h3 id="upload-bindings" tabindex="-1"><a class="header-anchor" href="#upload-bindings"><span>Upload bindings</span></a></h3>
<ul>
<li><code v-pre>uploads</code> / <code v-pre>:uploads</code> — Bind an array of <code v-pre>UploadedFile</code> instances if you handle uploads manually. Typically managed by the component when users pick files via the modal.</li>
<li><code v-pre>accept</code> — Exposes the computed <code v-pre>accept</code> string used for <code v-pre>&lt;input type=&quot;file&quot; accept=&quot;...&quot;&gt;</code>. Usually derived automatically from <code v-pre>mimes</code> and <code v-pre>extensions</code>.</li>
</ul>
<h3 id="events-outputs" tabindex="-1"><a class="header-anchor" href="#events-outputs"><span>Events &amp; outputs</span></a></h3>
<ul>
<li>The component emits events such as <code v-pre>media-added</code>, <code v-pre>media-uploaded</code>, <code v-pre>media-deleted</code>, <code v-pre>media-restored</code>, and <code v-pre>media-selected</code>.</li>
<li>Use the trait helpers on your models (<code v-pre>getMedia</code>, <code v-pre>getMediaUrls</code>, <code v-pre>getMediaUrl</code>, <code v-pre>attachMedia</code>, <code v-pre>syncMedia</code>) to work with the stored payloads.</li>
</ul>
</div></template>


