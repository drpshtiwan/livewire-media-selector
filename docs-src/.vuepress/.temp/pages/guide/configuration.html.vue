<template><div><h1 id="configuration-reference" tabindex="-1"><a class="header-anchor" href="#configuration-reference"><span>Configuration Reference</span></a></h1>
<p>All configuration lives in <code v-pre>config/media-selector.php</code>.<br>
Below is an overview of the most relevant options for tailoring the selector to your project.</p>
<h2 id="storage-directories" tabindex="-1"><a class="header-anchor" href="#storage-directories"><span>Storage &amp; directories</span></a></h2>
<ul>
<li><code v-pre>disk</code> — default filesystem disk used for uploads (<code v-pre>public</code>).</li>
<li><code v-pre>directory</code> — base directory relative to the disk root (<code v-pre>media</code>).</li>
<li><code v-pre>max_upload_kb</code> — upload size limit in kilobytes.</li>
</ul>
<p>Use the trait helper <code v-pre>getMediaUrls()</code> or <code v-pre>getMediaUrl()</code> to resolve storage URLs regardless of the chosen disk.</p>
<h2 id="upload-restrictions" tabindex="-1"><a class="header-anchor" href="#upload-restrictions"><span>Upload restrictions</span></a></h2>
<ul>
<li><code v-pre>allowed_extensions</code> — array of extensions (e.g. <code v-pre>['jpg', 'png', 'webp']</code>).</li>
<li><code v-pre>allowed_mimes</code> — full mime types or wildcard groups (e.g. <code v-pre>['image/*']</code>).</li>
<li>Provide <code v-pre>:extensions</code> or <code v-pre>:mimes</code> attributes on the component to override per-instance limits.</li>
</ul>
<h2 id="component-behavior" tabindex="-1"><a class="header-anchor" href="#component-behavior"><span>Component behavior</span></a></h2>
<ul>
<li><code v-pre>multiple</code> — allow multi-select.</li>
<li><code v-pre>can_upload</code> / <code v-pre>can_delete</code> — gate destructive actions.</li>
<li><code v-pre>can_see_trash</code> / <code v-pre>can_restore_trash</code> — expose soft-deleted media management.</li>
<li><code v-pre>restrict_to_current_user</code> — scope listings to the authenticated user’s uploads.</li>
</ul>
<h2 id="ui-flavor" tabindex="-1"><a class="header-anchor" href="#ui-flavor"><span>UI flavor</span></a></h2>
<p>Switch between Tailwind (default) and Bootstrap markup:</p>
<div class="language-php line-numbers-mode" data-highlighter="prismjs" data-ext="php"><pre v-pre><code class="language-php"><span class="line"><span class="token string single-quoted-string">'ui'</span> <span class="token operator">=></span> <span class="token function">env</span><span class="token punctuation">(</span><span class="token string single-quoted-string">'MEDIA_SELECTOR_UI'</span><span class="token punctuation">,</span> <span class="token string single-quoted-string">'tailwind'</span><span class="token punctuation">)</span><span class="token punctuation">,</span></span>
<span class="line"></span></code></pre>
<div class="line-numbers" aria-hidden="true" style="counter-reset:line-number 0"><div class="line-number"></div></div></div><p>Per component override:</p>
<div class="language-blade line-numbers-mode" data-highlighter="prismjs" data-ext="blade"><pre v-pre><code class="language-blade"><span class="line">&lt;livewire:media-selector ui=&quot;bootstrap&quot; /&gt;</span>
<span class="line"></span></code></pre>
<div class="line-numbers" aria-hidden="true" style="counter-reset:line-number 0"><div class="line-number"></div></div></div><h2 id="custom-filtering" tabindex="-1"><a class="header-anchor" href="#custom-filtering"><span>Custom filtering</span></a></h2>
<p>You can register an extra scope callback to modify the underlying query:</p>
<div class="language-php line-numbers-mode" data-highlighter="prismjs" data-ext="php"><pre v-pre><code class="language-php"><span class="line"><span class="token string single-quoted-string">'extra_scope'</span> <span class="token operator">=></span> <span class="token class-name class-name-fully-qualified static-context">App<span class="token punctuation">\</span>MediaSelector<span class="token punctuation">\</span>Scopes<span class="token punctuation">\</span>TeamScoped</span><span class="token operator">::</span><span class="token keyword">class</span><span class="token operator">.</span><span class="token string single-quoted-string">'@apply'</span><span class="token punctuation">,</span></span>
<span class="line"></span></code></pre>
<div class="line-numbers" aria-hidden="true" style="counter-reset:line-number 0"><div class="line-number"></div></div></div><p>Within the <code v-pre>apply</code> method, receive the query builder and the Livewire component instance to add constraints:</p>
<div class="language-php line-numbers-mode" data-highlighter="prismjs" data-ext="php"><pre v-pre><code class="language-php"><span class="line"><span class="token keyword">public</span> <span class="token keyword">function</span> <span class="token function-definition function">apply</span><span class="token punctuation">(</span><span class="token variable">$query</span><span class="token punctuation">,</span> <span class="token variable">$component</span><span class="token punctuation">)</span></span>
<span class="line"><span class="token punctuation">{</span></span>
<span class="line">    <span class="token variable">$query</span><span class="token operator">-></span><span class="token function">where</span><span class="token punctuation">(</span><span class="token string single-quoted-string">'team_id'</span><span class="token punctuation">,</span> <span class="token variable">$component</span><span class="token operator">-></span><span class="token property">teamId</span><span class="token punctuation">)</span><span class="token punctuation">;</span></span>
<span class="line"><span class="token punctuation">}</span></span>
<span class="line"></span></code></pre>
<div class="line-numbers" aria-hidden="true" style="counter-reset:line-number 0"><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div></div></div></div></template>


