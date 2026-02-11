<template><div><h1 id="livewire-integration" tabindex="-1"><a class="header-anchor" href="#livewire-integration"><span>Livewire Integration</span></a></h1>
<p>The media selector ships as a Livewire 3/4 component and is optimized for parent-child data binding.</p>
<h2 id="using-wire-model" tabindex="-1"><a class="header-anchor" href="#using-wire-model"><span>Using <code v-pre>wire:model</code></span></a></h2>
<div class="language-blade line-numbers-mode" data-highlighter="prismjs" data-ext="blade"><pre v-pre><code class="language-blade"><span class="line">&lt;livewire:media-selector wire:model=&quot;postMedia&quot; /&gt;</span>
<span class="line"></span></code></pre>
<div class="line-numbers" aria-hidden="true" style="counter-reset:line-number 0"><div class="line-number"></div></div></div><p>The component’s <code v-pre>value</code> property syncs with your parent component or Blade context.<br>
When the modal closes with a confirmed selection, the payload updates automatically.</p>
<h2 id="interacting-from-a-livewire-parent" tabindex="-1"><a class="header-anchor" href="#interacting-from-a-livewire-parent"><span>Interacting from a Livewire parent</span></a></h2>
<div class="language-php line-numbers-mode" data-highlighter="prismjs" data-ext="php"><pre v-pre><code class="language-php"><span class="line"><span class="token keyword">class</span> <span class="token class-name-definition class-name">EditPost</span> <span class="token keyword">extends</span> <span class="token class-name">Component</span></span>
<span class="line"><span class="token punctuation">{</span></span>
<span class="line">    <span class="token keyword">public</span> <span class="token keyword type-declaration">array</span> <span class="token variable">$postMedia</span> <span class="token operator">=</span> <span class="token punctuation">[</span><span class="token punctuation">]</span><span class="token punctuation">;</span></span>
<span class="line"></span>
<span class="line">    <span class="token keyword">public</span> <span class="token keyword">function</span> <span class="token function-definition function">mount</span><span class="token punctuation">(</span><span class="token class-name type-declaration">Post</span> <span class="token variable">$post</span><span class="token punctuation">)</span><span class="token punctuation">:</span> <span class="token keyword return-type">void</span></span>
<span class="line">    <span class="token punctuation">{</span></span>
<span class="line">        <span class="token variable">$this</span><span class="token operator">-></span><span class="token property">postMedia</span> <span class="token operator">=</span> <span class="token variable">$post</span><span class="token operator">-></span><span class="token function">getMediaPayload</span><span class="token punctuation">(</span><span class="token string single-quoted-string">'gallery'</span><span class="token punctuation">)</span><span class="token punctuation">;</span></span>
<span class="line">    <span class="token punctuation">}</span></span>
<span class="line"></span>
<span class="line">    <span class="token keyword">public</span> <span class="token keyword">function</span> <span class="token function-definition function">save</span><span class="token punctuation">(</span><span class="token punctuation">)</span><span class="token punctuation">:</span> <span class="token keyword return-type">void</span></span>
<span class="line">    <span class="token punctuation">{</span></span>
<span class="line">        <span class="token variable">$this</span><span class="token operator">-></span><span class="token function">validate</span><span class="token punctuation">(</span><span class="token punctuation">[</span></span>
<span class="line">            <span class="token string single-quoted-string">'postMedia'</span> <span class="token operator">=></span> <span class="token punctuation">[</span><span class="token string single-quoted-string">'array'</span><span class="token punctuation">]</span><span class="token punctuation">,</span></span>
<span class="line">        <span class="token punctuation">]</span><span class="token punctuation">)</span><span class="token punctuation">;</span></span>
<span class="line"></span>
<span class="line">        <span class="token variable">$this</span><span class="token operator">-></span><span class="token property">post</span><span class="token operator">-></span><span class="token function">syncMedia</span><span class="token punctuation">(</span><span class="token variable">$this</span><span class="token operator">-></span><span class="token property">postMedia</span><span class="token punctuation">,</span> <span class="token string single-quoted-string">'gallery'</span><span class="token punctuation">)</span><span class="token punctuation">;</span></span>
<span class="line">    <span class="token punctuation">}</span></span>
<span class="line"><span class="token punctuation">}</span></span>
<span class="line"></span></code></pre>
<div class="line-numbers" aria-hidden="true" style="counter-reset:line-number 0"><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div></div></div><h3 id="listen-for-events" tabindex="-1"><a class="header-anchor" href="#listen-for-events"><span>Listen for events</span></a></h3>
<p>The component emits several events:</p>
<ul>
<li><code v-pre>media-added</code></li>
<li><code v-pre>media-uploaded</code></li>
<li><code v-pre>media-deleted</code></li>
<li><code v-pre>media-restored</code></li>
<li><code v-pre>media-selected</code></li>
</ul>
<p>Handling an event:</p>
<div class="language-php line-numbers-mode" data-highlighter="prismjs" data-ext="php"><pre v-pre><code class="language-php"><span class="line"><span class="token keyword">protected</span> <span class="token variable">$listeners</span> <span class="token operator">=</span> <span class="token punctuation">[</span></span>
<span class="line">    <span class="token string single-quoted-string">'media-selected'</span> <span class="token operator">=></span> <span class="token string single-quoted-string">'handleMediaSelected'</span><span class="token punctuation">,</span></span>
<span class="line"><span class="token punctuation">]</span><span class="token punctuation">;</span></span>
<span class="line"></span>
<span class="line"><span class="token keyword">public</span> <span class="token keyword">function</span> <span class="token function-definition function">handleMediaSelected</span><span class="token punctuation">(</span><span class="token variable">$payload</span><span class="token punctuation">)</span><span class="token punctuation">:</span> <span class="token keyword return-type">void</span></span>
<span class="line"><span class="token punctuation">{</span></span>
<span class="line">    <span class="token function">ray</span><span class="token punctuation">(</span><span class="token string single-quoted-string">'Selected'</span><span class="token punctuation">,</span> <span class="token variable">$payload</span><span class="token punctuation">)</span><span class="token punctuation">;</span></span>
<span class="line"><span class="token punctuation">}</span></span>
<span class="line"></span></code></pre>
<div class="line-numbers" aria-hidden="true" style="counter-reset:line-number 0"><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div><div class="line-number"></div></div></div><h2 id="accessing-urls-directly" tabindex="-1"><a class="header-anchor" href="#accessing-urls-directly"><span>Accessing URLs directly</span></a></h2>
<p>When rendering preview thumbnails outside the selector, use the trait helpers:</p>
<div class="language-php line-numbers-mode" data-highlighter="prismjs" data-ext="php"><pre v-pre><code class="language-php"><span class="line"><span class="token variable">$post</span><span class="token operator">-></span><span class="token function">getMediaUrl</span><span class="token punctuation">(</span><span class="token string single-quoted-string">'gallery'</span><span class="token punctuation">)</span><span class="token punctuation">;</span> <span class="token comment">// first URL</span></span>
<span class="line"><span class="token variable">$post</span><span class="token operator">-></span><span class="token function">getMediaUrls</span><span class="token punctuation">(</span><span class="token string single-quoted-string">'gallery'</span><span class="token punctuation">)</span><span class="token punctuation">;</span> <span class="token comment">// all URLs</span></span>
<span class="line"></span></code></pre>
<div class="line-numbers" aria-hidden="true" style="counter-reset:line-number 0"><div class="line-number"></div><div class="line-number"></div></div></div><p>This ensures cache-friendly lookups that respect eager-loaded relations and disk-specific URL generation.</p>
</div></template>


