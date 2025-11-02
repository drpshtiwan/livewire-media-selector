<div data-lms  data-lms-ui="{{ $ui }}" dir="{{ in_array(app()->getLocale(), ['ar','ckb','fa','ur']) ? 'rtl' : 'ltr' }}" x-data="{toastVisible:false, toastMessage:'', toastTimer:null, showToast(msg){ this.toastMessage=msg; this.toastVisible=true; clearTimeout(this.toastTimer); this.toastTimer=setTimeout(()=>{ this.toastVisible=false }, 1500); }}" x-on:toast.window="showToast($event.detail.message)" x-on:keydown.escape.window.prevent="$wire.closeModal()">
    <div class="">
        <div class="lms-flex lms-items-center lms-gap-2 lms-ml-auto">
            @if($value)
                <button type="button" class="lms-px-4 lms-py-3 lms-text-base lms-border lms-rounded" wire:click="clearPreview">{{ __('media-selector::messages.clear') }}</button>
            @endif
            <button type="button" class="lms-px-4 lms-py-3 lms-text-base lms-bg-black hover:lms-bg-gray-800 lms-text-white lms-rounded-lg" wire:click="openModal">{{ __('media-selector::messages.choose_media') }}</button>
        </div>
    </div>



    @if(is_array($value) && count($value))
        <div class="lms-mt-3 lms-grid lms-grid-cols-2 sm:lms-grid-cols-3 md:lms-grid-cols-5 lg:lms-grid-cols-6 xl:lms-grid-cols-8 lms-gap-2">
            @foreach($value as $val)
                @php($__path = is_array($val) ? ($val['path'] ?? '') : $val)
                @php($__id = is_array($val) ? ($val['id'] ?? null) : null)
                @php($__filename = is_array($val) ? ($val['filename'] ?? null) : null)
                @php($__mimeProvided = is_array($val) ? ($val['mime'] ?? null) : null)
                @php($__sizeProvided = is_array($val) ? ($val['size'] ?? null) : null)
                <div class="lms-relative lms-border lms-rounded-lg lms-overflow-hidden lms-group lms-aspect-square lms-cursor-move" draggable="true"
                     x-on:dragstart="$event.dataTransfer.setData('from','{{ $loop->index }}')"
                     x-on:dragover.prevent
                     x-on:drop.prevent="$wire.moveValueItem(parseInt($event.dataTransfer.getData('from')), {{ $loop->index }})">
                    @php($mime = $__path ? (Storage::disk($this->disk)->mimeType($__path) ?? null) : null)
                    @php($ext = $__path ? strtolower((string) pathinfo($__path, PATHINFO_EXTENSION)) : null)
                    @php($isImageExt = in_array($ext, ['jpg','jpeg','png','gif','webp','bmp','svg','avif','jfif']))
                    @if((($__mimeProvided ?? $mime) && str_starts_with((string)($__mimeProvided ?? $mime), 'image/')) || $isImageExt)
                        <img src="{{ $__path ? Storage::disk($this->disk)->url($__path) : '' }}" alt="Selected" class="lms-w-full lms-h-full lms-object-cover" />
                    @else
                        <div class="lms-w-full lms-h-full lms-grid lms-place-items-center lms-text-[11px] lms-text-gray-600 lms-bg-gray-50">
                            <div class="lms-flex lms-items-center lms-gap-1">
                                <svg viewBox="0 0 24 24" class="lms-w-5 lms-h-5 lms-text-gray-500" fill="currentColor" aria-hidden="true">
                                    <path d="M6 2a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8.828a2 2 0 0 0-.586-1.414l-4.828-4.828A2 2 0 0 0 13.172 2H6zM14 3.414 19.586 9H15a1 1 0 0 1-1-1V3.414z"/>
                                </svg>
                                <span class="lms-font-medium">{{ strtoupper($__path ? (pathinfo($__path, PATHINFO_EXTENSION) ?: 'FILE') : 'FILE') }}</span>
                            </div>
                        </div>
                    @endif
                    <div class="lms-absolute lms-top-1.5 lms-right-1.5 lms-z-10 lms-flex lms-items-center lms-gap-1 lms-transition lms-opacity-0 group-hover:lms-opacity-100 lms-pointer-events-none group-hover:lms-pointer-events-auto">
                        <button type="button" class="lms-inline-flex lms-items-center lms-justify-center lms-w-6 lms-h-6 lms-rounded-md lms-bg-white/90 lms-border hover:lms-bg-gray-100" title="Remove" x-on:click.prevent="$wire.removeFromValue({{ $__id ? (int) $__id : ('\'' . addslashes($__path) . '\'') }})" aria-label="Remove">
                            <svg viewBox="0 0 24 24" class="lms-w-3.5 lms-h-3.5 lms-text-gray-700" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M18 6 6 18M6 6l12 12" />
                            </svg>
                        </button>
                        <button type="button" class="lms-inline-flex lms-items-center lms-justify-center lms-w-6 lms-h-6 lms-rounded-md lms-bg-white/90 lms-border hover:lms-bg-gray-100" title="Copy URL" x-on:click.stop.prevent="const __t = @js($__path ? Storage::disk($this->disk)->url($__path) : ''); if (navigator.clipboard && window.isSecureContext) { navigator.clipboard.writeText(__t); } else { const ta=document.createElement('textarea'); ta.value=__t; ta.setAttribute('readonly',''); ta.style.position='fixed'; ta.style.top='-9999px'; document.body.appendChild(ta); ta.select(); document.execCommand('copy'); document.body.removeChild(ta); } $dispatch('toast', { message: @js(($mime && str_starts_with($mime,'video/')) ? 'Video URL copied' : (($mime && str_starts_with($mime,'audio/')) ? 'Audio URL copied' : 'URL copied')) });" aria-label="Copy URL">
                            <svg viewBox="0 0 24 24" class="lms-w-3.5 lms-h-3.5 lms-text-gray-700" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
                            </svg>
                        </button>
                        @if($__filename || $__mimeProvided || $__sizeProvided)
                            <button type="button" class="lms-inline-flex lms-items-center lms-justify-center lms-w-6 lms-h-6 lms-rounded-md lms-bg-white/90 lms-border hover:lms-bg-gray-100" title="{{ __('media-selector::messages.details') }}" x-on:click.stop.prevent="$dispatch('toast', { message: '{{ addslashes(__('media-selector::messages.details')) }}: {{ addslashes($__filename ?? (pathinfo($__path, PATHINFO_BASENAME) ?: '')) }} ({{ addslashes($__mimeProvided ?? ($mime ?? 'n/a')) }}) @if($__sizeProvided) {{ (int)($__sizeProvided/1024) }}KB @endif' })" aria-label="{{ __('media-selector::messages.details') }}">
                                <svg viewBox="0 0 24 24" class="lms-w-3.5 lms-h-3.5 lms-text-gray-700" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="8" x2="12" y2="12"></line>
                                    <line x1="12" y1="16" x2="12" y2="16"></line>
                                </svg>
                            </button>
                        @endif
                    </div>
                    <div class="lms-absolute lms-bottom-0 lms-left-0 lms-right-0 lms-bg-black/60 lms-text-white lms-text-[10px] lms-px-1.5 lms-py-0.5 lms-truncate">{{ $__filename ?? ($__path ? pathinfo($__path, PATHINFO_BASENAME) : '') }}</div>
                    <div class="lms-absolute lms-bottom-1.5 lms-left-1.5 lms-text-[9px] lms-bg-white/80 lms-border lms-rounded lms-px-1 lms-py-0.5 lms-opacity-0 group-hover:lms-opacity-100 lms-transition">Drag</div>
                </div>
            @endforeach
        </div>
    @elseif(is_string($value) && $value)
        @php($__mime = Storage::disk($this->disk)->mimeType($value) ?? null)
        @php($__ext = strtolower((string) pathinfo($value, PATHINFO_EXTENSION)))
        @php($__isImageExt = in_array($__ext, ['jpg','jpeg','png','gif','webp','bmp','svg','avif','jfif']))
        <div class="lms-mt-3">
            <div class="lms-relative lms-w-64 lms-h-64 lms-border lms-rounded lms-overflow-hidden lms-bg-gray-50 lms-group">
                @if((($__mime ?? null) && str_starts_with($__mime, 'image/')) || $__isImageExt)
                    <img src="{{ Storage::disk($this->disk)->url($value) }}" alt="Selected media" class="lms-w-full lms-h-full lms-object-cover" />
                @else
                    <div class="lms-absolute lms-inset-0 lms-grid lms-place-items-center lms-text-sm lms-text-gray-600">
                        <div class="lms-flex lms-items-center lms-gap-2">
                            <svg viewBox="0 0 24 24" class="lms-w-7 lms-h-7 lms-text-gray-500" fill="currentColor" aria-hidden="true">
                                <path d="M6 2a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8.828a2 2 0 0 0-.586-1.414l-4.828-4.828A2 2 0 0 0 13.172 2H6zM14 3.414 19.586 9H15a1 1 0 0 1-1-1V3.414z"/>
                            </svg>
                            <span class="lms-font-medium">{{ strtoupper(pathinfo($value, PATHINFO_EXTENSION) ?: 'FILE') }}</span>
                        </div>
                    </div>
                @endif
                <button type="button" class="lms-absolute lms-top-2 lms-right-2 lms-z-10 lms-inline-flex lms-items-center lms-justify-center lms-w-8 lms-h-8 lms-rounded-md lms-bg-white/90 lms-border hover:lms-bg-gray-100 lms-opacity-0 group-hover:lms-opacity-100 lms-pointer-events-none group-hover:lms-pointer-events-auto lms-transition" title="Copy URL" x-on:click.stop.prevent="const __t = @js(Storage::disk($this->disk)->url($value)); if (navigator.clipboard && window.isSecureContext) { navigator.clipboard.writeText(__t); } else { const ta=document.createElement('textarea'); ta.value=__t; ta.setAttribute('readonly',''); ta.style.position='fixed'; ta.style.top='-9999px'; document.body.appendChild(ta); ta.select(); document.execCommand('copy'); document.body.removeChild(ta); } $dispatch('toast', { message: @js((($__mime ?? null) && str_starts_with($__mime,'video/')) ? 'Video URL copied' : (((($__mime ?? null)) && str_starts_with($__mime,'audio/')) ? 'Audio URL copied' : 'URL copied')) });" aria-label="Copy URL">
                    <svg viewBox="0 0 24 24" class="lms-w-4 lms-h-4 lms-text-gray-700" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
                    </svg>
                </button>
            </div>
        </div>
    @endif

    @if($showModal)
        <div class="lms-fixed lms-inset-0" style="z-index: 1200;">
            <div class="lms-absolute lms-inset-0 lms-bg-black/40" wire:click="closeModal"></div>
            <div class="lms-absolute lms-inset-0 lms-flex lms-items-center lms-justify-center lms-p-6">
                <div class="lms-bg-white lms-rounded-xl lms-shadow-2xl lms-w-full lms-max-w-7xl lms-max-h-[75vh] lms-overflow-hidden lms-flex lms-flex-col" id="lms-modal-{{ $ui }}">
                    <div class="lms-px-6 lms-pt-4 lms-pb-3 lms-border-b lms-bg-gray-50">
                        <div class="lms-flex lms-items-center lms-gap-4">
                            <h3 class="lms-text-sm lms-font-semibold lms-text-gray-800">{{ __('media-selector::messages.media_library') }}</h3>
                            <div class="lms-flex lms-items-center lms-gap-2">
                                <button type="button" class="lms-inline-flex lms-items-center lms-px-3 lms-py-1.5 lms-text-sm lms-rounded-md lms-border {{ $activeTab==='browse' ? 'lms-bg-white lms-border-gray-300' : 'lms-bg-gray-100 lms-border-transparent lms-text-gray-700' }}" wire:click="changeTab('browse')">{{ __('media-selector::messages.tab_select_file') }}</button>
                                @if($canUpload)
                                    <button type="button" class="lms-inline-flex lms-items-center lms-px-3 lms-py-1.5 lms-text-sm lms-rounded-md lms-border {{ $activeTab==='upload' ? 'lms-bg-white lms-border-gray-300' : 'lms-bg-gray-100 lms-border-transparent lms-text-gray-700' }}" wire:click="changeTab('upload')">{{ __('media-selector::messages.tab_upload_new') }}</button>
                                @endif
                                @if($canSeeTrash)
                                    <button type="button" class="lms-inline-flex lms-items-center lms-px-3 lms-py-1.5 lms-text-sm lms-rounded-md lms-border {{ $activeTab==='trash' ? 'lms-bg-white lms-border-gray-300' : 'lms-bg-gray-100 lms-border-transparent lms-text-gray-700' }}" wire:click="changeTab('trash')">{{ __('media-selector::messages.tab_trash') }}</button>
                                @endif
                            </div>
                            <button type="button" class="lms-ml-auto lms-inline-flex lms-items-center lms-justify-center lms-w-8 lms-h-8 lms-rounded-md hover:lms-bg-gray-100" wire:click="closeModal" aria-label="Close">
                                ✕
                            </button>
                        </div>
                    </div>

                    @if($activeTab==='browse')
                        <div class="lms-px-6 lms-py-3 lms-border-b lms-flex lms-items-center lms-gap-3">
                            <select class="lms-border lms-rounded lms-px-2 lms-py-1.5 lms-text-sm" wire:model.live="sort">
                                <option value="newest">{{ __('media-selector::messages.sort_newest') }}</option>
                                <option value="oldest">{{ __('media-selector::messages.sort_oldest') }}</option>
                                <option value="name_asc">{{ __('media-selector::messages.sort_a_z') }}</option>
                                <option value="name_desc">{{ __('media-selector::messages.sort_z_a') }}</option>
                            </select>
                            <label class="lms-text-sm lms-flex lms-items-center lms-gap-2">
                                <input type="checkbox" class="lms-rounded" wire:model.live="selectedOnly">
                                {{ __('media-selector::messages.selected_only') }}
                            </label>
                            <div class="lms-flex lms-items-center lms-gap-2 lms-flex-1">
                                <input type="text" class="lms-flex-1 lms-border lms-rounded lms-px-3 lms-py-1.5 lms-text-sm" placeholder="{{ __('media-selector::messages.search_your_files') }}" wire:model.debounce.300ms="search" />
                                <button type="button" class="lms-inline-flex lms-items-center lms-gap-1 lms-px-3 lms-py-1.5 lms-text-sm lms-rounded-md lms-border" wire:click="$refresh" aria-label="Search">
                                    <svg viewBox="0 0 24 24" class="lms-w-4 lms-h-4 lms-text-gray-700" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <circle cx="11" cy="11" r="7"></circle>
                                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                    </svg>
                                    <span>{{ __('media-selector::messages.search') ?? 'Search' }}</span>
                                </button>
                            </div>
                        </div>

                        <div class="lms-px-6 lms-py-5 lms-overflow-auto">
                            @if($media->count() === 0)
                                <div class="lms-text-sm lms-text-gray-500">{{ __('media-selector::messages.no_media_found') }}</div>
                            @else
                                <div class="lms-grid lms-grid-cols-2 sm:lms-grid-cols-3 md:lms-grid-cols-4 lg:lms-grid-cols-6 lms-gap-4">
                                    @foreach($media as $item)
                                        <div class="lms-relative lms-group lms-aspect-square" wire:key="media-{{ $item->id }}">
                                            <button type="button" class="lms-absolute lms-inset-0 lms-z-0 lms-border lms-rounded-lg lms-overflow-hidden hover:lms-shadow" wire:click.prevent="toggleSelect({{ $item->id }})">

                                                @php($__extLower = strtolower((string) pathinfo($item->filename ?? '', PATHINFO_EXTENSION)))
                                                @php($__isImageByExt = in_array($__extLower, ['jpg','jpeg','png','gif','webp','bmp','svg','avif','jfif']))
                                                @if((method_exists($item, 'isPreviewable') ? $item->isPreviewable() : false) || ($item->mime && str_starts_with((string)$item->mime, 'image/')) || $__isImageByExt)
                                                    <img src="{{ $item->url }}" alt="{{ $item->filename }}" class="lms-w-full lms-h-full lms-object-cover" />
                                                @else
                                                    <div class="lms-w-full lms-h-full lms-grid lms-place-items-center lms-text-sm lms-text-gray-600 lms-bg-gray-50">
                                                        <div class="lms-flex lms-items-center lms-gap-1">
                                                            <svg viewBox="0 0 24 24" class="lms-w-6 lms-h-6 lms-text-gray-500" fill="currentColor" aria-hidden="true">
                                                                <path d="M6 2a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8.828a2 2 0 0 0-.586-1.414l-4.828-4.828A2 2 0 0 0 13.172 2H6zM14 3.414 19.586 9H15a1 1 0 0 1-1-1V3.414z"/>
                                                            </svg>
                                                            <span class="lms-font-medium">{{ strtoupper(pathinfo($item->filename, PATHINFO_EXTENSION)) ?: 'FILE' }}</span>
                                                        </div>
                                                    </div>
                                                @endif
                                                <div class="lms-absolute lms-inset-0 lms-bg-black/0 group-hover:lms-bg-black/10 lms-transition"></div>
                                                <div class="lms-absolute lms-bottom-0 lms-left-0 lms-right-0 lms-bg-black/60 lms-text-white lms-text-[11px] lms-px-1.5 lms-py-1 lms-truncate">{{ ($item->metadata['original'] ?? null) ?: $item->filename }}</div>
                                                @if(in_array($item->id, $selectedIds))
                                                    <div class="lms-absolute lms-inset-0 lms-rounded-lg lms-ring-4 lms-ring-indigo-600 lms-ring-offset-2 lms-ring-offset-white lms-pointer-events-none"></div>
                                                    <div class="lms-absolute lms-top-2 lms-left-2 lms-inline-flex lms-items-center lms-justify-center lms-w-6 lms-h-6 lms-rounded-full lms-bg-indigo-600 lms-text-white lms-text-sm">✓</div>
                                                @endif
                                            </button>
                                            <div class="lms-absolute lms-top-2 lms-right-2 lms-z-10 lms-flex lms-items-center lms-gap-1 lms-opacity-0 group-hover:lms-opacity-100 lms-pointer-events-none group-hover:lms-pointer-events-auto lms-transition">
                                                <button type="button" class="lms-inline-flex lms-items-center lms-justify-center lms-w-7 lms-h-7 lms-rounded-md lms-bg-white/90 lms-border hover:lms-bg-gray-100" title="{{ __('media-selector::messages.url_copied') }}" x-on:click.stop.prevent="const __t = @js($item->url); if (navigator.clipboard && window.isSecureContext) { navigator.clipboard.writeText(__t); } else { const ta=document.createElement('textarea'); ta.value=__t; ta.setAttribute('readonly',''); ta.style.position='fixed'; ta.style.top='-9999px'; document.body.appendChild(ta); ta.select(); document.execCommand('copy'); document.body.removeChild(ta); } $dispatch('toast', { message: @js(($item->mime && str_starts_with($item->mime,'video/')) ? __('media-selector::messages.video_url_copied') : (($item->mime && str_starts_with($item->mime,'audio/')) ? __('media-selector::messages.audio_url_copied') : __('media-selector::messages.url_copied'))) });" aria-label="{{ __('media-selector::messages.url_copied') }}">
                                                    <svg viewBox="0 0 24 24" class="lms-w-4 lms-h-4 lms-text-gray-700" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
                                                    </svg>
                                                </button>
                                                <button type="button" class="lms-inline-flex lms-items-center lms-justify-center lms-w-7 lms-h-7 lms-rounded-md lms-bg-white/90 lms-border hover:lms-bg-gray-100" title="{{ __('media-selector::messages.details') }}" x-on:click.stop.prevent="$dispatch('toast', { message: '{{ addslashes(__('media-selector::messages.details')) }}: {{ addslashes(($item->metadata['original'] ?? null) ?: $item->filename) }} ({{ addslashes($item->mime ?? 'n/a') }}) {{ (int)($item->size/1024) }}KB' })" aria-label="{{ __('media-selector::messages.details') }}">
                                                    <svg viewBox="0 0 24 24" class="lms-w-4 lms-h-4 lms-text-gray-700" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                        <circle cx="12" cy="12" r="10"></circle>
                                                        <line x1="12" y1="8" x2="12" y2="12"></line>
                                                        <line x1="12" y1="16" x2="12" y2="16"></line>
                                                    </svg>
                                                </button>
                                                @if($canDelete ?? false)
                                                    <button type="button" class="lms-inline-flex lms-items-center lms-justify-center lms-w-7 lms-h-7 lms-rounded-md lms-bg-white/90 lms-border hover:lms-bg-gray-100" title="{{ __('media-selector::messages.delete') }}" x-on:click.stop.prevent="if(confirm(@js(__('media-selector::messages.delete_this_file_q')))) $wire.deleteMedia({{ $item->id }})" aria-label="{{ __('media-selector::messages.delete') }}">
                                                        <svg viewBox="0 0 24 24" class="lms-w-4 lms-h-4 lms-text-red-600" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                            <polyline points="3 6 5 6 21 6"></polyline>
                                                            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                            <line x1="10" y1="11" x2="10" y2="17"></line>
                                                            <line x1="14" y1="11" x2="14" y2="17"></line>
                                                        </svg>
                                                    </button>

                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif

                    @if($activeTab==='trash' && $canSeeTrash)
                        <div class="lms-px-6 lms-py-5 lms-overflow-auto">
                            @if($media->count() === 0)
                                <div class="lms-text-sm lms-text-gray-500">{{ __('media-selector::messages.trash_empty') }}</div>
                            @else
                                <div class="lms-grid lms-grid-cols-2 sm:lms-grid-cols-3 md:lms-grid-cols-4 lg:lms-grid-cols-6 lms-gap-4">
                                    @foreach($media as $item)
                                        <div class="lms-relative lms-group lms-aspect-square" wire:key="trash-{{ $item->id }}">
                                            <button type="button" class="lms-absolute lms-inset-0 lms-z-0 lms-border lms-rounded-lg lms-overflow-hidden hover:lms-shadow" wire:click="toggleSelect({{ $item->id }})">
                                                @php($__extLower = strtolower((string) pathinfo($item->filename ?? '', PATHINFO_EXTENSION)))
                                                @php($__isImageByExt = in_array($__extLower, ['jpg','jpeg','png','gif','webp','bmp','svg','avif','jfif']))
                                                @if((method_exists($item, 'isPreviewable') ? $item->isPreviewable() : false) || ($item->mime && str_starts_with((string)$item->mime, 'image/')) || $__isImageByExt)
                                                    <img src="{{ $item->url }}" alt="{{ $item->filename }}" class="lms-w-full lms-h-full lms-object-cover" />
                                                @else
                                                    <div class="lms-w-full lms-h-full lms-grid lms-place-items-center lms-text-sm lms-text-gray-600 lms-bg-gray-50">
                                                        <div class="lms-flex lms-items-center lms-gap-1">
                                                            <svg viewBox="0 0 24 24" class="lms-w-6 lms-h-6 lms-text-gray-500" fill="currentColor" aria-hidden="true">
                                                                <path d="M6 2a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8.828a2 2 0 0 0-.586-1.414l-4.828-4.828A2 2 0 0 0 13.172 2H6zM14 3.414 19.586 9H15a1 1 0 0 1-1-1V3.414z"/>
                                                            </svg>
                                                            <span class="lms-font-medium">{{ strtoupper(pathinfo($item->filename, PATHINFO_EXTENSION)) ?: 'FILE' }}</span>
                                                        </div>
                                                    </div>
                                                @endif
                                                <div class="lms-absolute lms-inset-0 lms-bg-black/0 group-hover:lms-bg-black/10 lms-transition"></div>
                                                <div class="lms-absolute lms-bottom-0 lms-left-0 lms-right-0 lms-bg-black/60 lms-text-white lms-text-[11px] lms-px-1.5 lms-py-1 lms-truncate">{{ ($item->metadata['original'] ?? null) ?: $item->filename }}</div>
                                                @if(in_array($item->id, $selectedIds))
                                                    <div class="lms-absolute lms-inset-0 lms-rounded-lg lms-ring-4 lms-ring-indigo-600 lms-ring-offset-2 lms-ring-offset-white lms-pointer-events-none"></div>
                                                    <div class="lms-absolute lms-top-2 lms-left-2 lms-inline-flex lms-items-center lms-justify-center lms-w-6 lms-h-6 lms-rounded-full lms-bg-indigo-600 lms-text-white lms-text-sm">✓</div>
                                                @endif
                                            </button>
                                            <div class="lms-absolute lms-top-2 lms-right-2 lms-z-10 lms-flex lms-items-center lms-gap-1 lms-opacity-0 group-hover:lms-opacity-100 lms-pointer-events-none group-hover:lms-pointer-events-auto lms-transition">
                                                <button type="button" class="lms-inline-flex lms-items-center lms-justify-center lms-w-7 lms-h-7 lms-rounded-md lms-bg-white/90 lms-border hover:lms-bg-gray-100" title="{{ __('media-selector::messages.url_copied') }}" x-on:click.stop.prevent="const __t = @js($item->url); if (navigator.clipboard && window.isSecureContext) { navigator.clipboard.writeText(__t); } else { const ta=document.createElement('textarea'); ta.value=__t; ta.setAttribute('readonly',''); ta.style.position='fixed'; ta.style.top='-9999px'; document.body.appendChild(ta); ta.select(); document.execCommand('copy'); document.body.removeChild(ta); } $dispatch('toast', { message: @js(__('media-selector::messages.url_copied')) });" aria-label="{{ __('media-selector::messages.url_copied') }}">
                                                    <svg viewBox="0 0 24 24" class="lms-w-4 lms-h-4 lms-text-gray-700" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
                                                    </svg>
                                                </button>
                                                @if($canRestoreTrash)
                                                    <button type="button" class="lms-inline-flex lms-items-center lms-justify-center lms-w-7 lms-h-7 lms-rounded-md lms-bg-white/90 lms-border hover:lms-bg-gray-100" title="{{ __('media-selector::messages.restore') }}" x-on:click.stop.prevent="if(confirm(@js(__('media-selector::messages.restore_this_file_q')))) $wire.restoreMedia({{ $item->id }})" aria-label="{{ __('media-selector::messages.restore') }}">
                                                        <svg viewBox="0 0 24 24" class="lms-w-4 lms-h-4 lms-text-green-600" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                            <polyline points="1 4 1 10 7 10"></polyline>
                                                            <path d="M3.51 15a9 9 0 1 0 .49-5m-2.99 0H7" />
                                                        </svg>
                                                    </button>
                                                @endif
                                                @if($canDelete)
                                                     <button type="button" class="lms-inline-flex lms-items-center lms-justify-center lms-w-7 lms-h-7 lms-rounded-md lms-bg-white/90 lms-border hover:lms-bg-gray-100" title="{{ __('media-selector::messages.delete_permanently') }}" x-on:click.stop.prevent="if(confirm(@js(__('media-selector::messages.delete_permanently_q')))) $wire.forceDeleteMedia({{ $item->id }})" aria-label="{{ __('media-selector::messages.delete_permanently') }}">
                                                        <svg viewBox="0 0 24 24" class="lms-w-4 lms-h-4 lms-text-red-700" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                            <path d="M3 6h18"/>
                                                            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                                            <path d="M10 11v6M14 11v6"/>
                                                            <path d="M9 6V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/>
                                                        </svg>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif

                    @if($activeTab==='upload' && $canUpload)
                        <div class="lms-px-6 lms-py-5 lms-space-y-4" x-data="{progress:0}" x-on:livewire-upload-progress.window="progress = $event.detail.progress">
                            @error('uploads')
                                <div class="lms-px-4 lms-py-2 lms-text-sm lms-text-red-600">{{ $message }}</div>
                            @enderror
                            @error('uploads.*')
                                <div class="lms-px-4 lms-py-2 lms-text-sm lms-text-red-600">{{ $message }}</div>
                            @enderror
                            <div x-data="{
                                    isOver:false,
                                    handleDrop(e){
                                        this.isOver=false;
                                        const files = [];
                                        if (e.dataTransfer && e.dataTransfer.items && e.dataTransfer.items.length){
                                            for (const item of e.dataTransfer.items){
                                                if (item.kind === 'file'){ const f = item.getAsFile(); if (f) files.push(f); }
                                            }
                                        } else if (e.dataTransfer && e.dataTransfer.files){
                                            for (const f of e.dataTransfer.files){ files.push(f); }
                                        }
                                        if (!files.length) return;
                                        // Prefer triggering the hidden input so wire:model picks it up reliably
                                        const dt = new DataTransfer();
                                        for (const f of files) dt.items.add(f);
                                        if (this.$refs.file) {
                                            this.$refs.file.files = dt.files;
                                            this.$refs.file.dispatchEvent(new Event('input', { bubbles: true }));
                                            this.$refs.file.dispatchEvent(new Event('change', { bubbles: true }));
                                        } else {
                                            // fallback to direct upload API
                                            $wire.upload('uploads', files);
                                        }
                                    }
                                }"
                                class="lms-border-2 lms-border-dashed lms-border-gray-300 lms-rounded-lg lms-p-8 lms-text-center hover:lms-bg-gray-50"
                                 x-on:dragenter.prevent.stop="isOver=true"
                                 x-on:dragover.prevent.stop="isOver=true"
                                 x-on:dragleave.prevent.stop="isOver=false"
                                 x-on:drop.prevent.stop="handleDrop($event)"
                                 :class="isOver ? 'lms-ring-2 lms-ring-indigo-500 lms-bg-indigo-50' : ''">
                                <input type="file" id="uploads-input" class="lms-hidden" wire:model="uploads" multiple accept="{{ $accept }}" x-ref="file" />
                                <label for="uploads-input" class="lms-cursor-pointer lms-block">
                                    <div class="lms-text-sm lms-text-gray-700">{{ __('media-selector::messages.click_to_upload') }}</div>
                                    <div class="lms-text-sm lms-text-gray-500">
                                        {{ __('media-selector::messages.allowed') }}: {{ implode(', ', $allowedTokens ?? []) }}
                                        {{ __('media-selector::messages.up_to_kb_each', ['size' => (int) config('media-selector.max_upload_kb', 5120)]) }}
                                        @if($requireWidth || $requireHeight)
                                            · {{ $requireWidth ? $requireWidth . 'px' : '*' }} × {{ $requireHeight ? $requireHeight . 'px' : '*' }}
                                        @elseif($requireAspectRatio)
                                            · {{ $requireAspectRatio }}
                                        @endif
                                    </div>
                                </label>
                            </div>
                            @if($uploads)
                                <div class="lms-space-y-3">
                                    <div class="lms-grid lms-grid-cols-2 sm:lms-grid-cols-3 md:lms-grid-cols-4 lg:lms-grid-cols-6 lms-gap-4">
                                        @foreach($uploads as $temp)
                                            <div class="lms-border lms-rounded-lg lms-p-2 lms-flex lms-flex-col lms-items-center lms-text-center lms-relative">
                                                <div class="lms-w-24 lms-h-24 lms-rounded lms-overflow-hidden lms-border lms-grid lms-place-items-center">
                                                    @php($tMime = method_exists($temp, 'getClientMimeType') ? $temp->getClientMimeType() : null)
                                                    @php($tName = method_exists($temp,'getClientOriginalName') ? (string) $temp->getClientOriginalName() : '')
                                                    @php($tExtLower = strtolower((string) pathinfo($tName, PATHINFO_EXTENSION)))
                                                    @php($isImageExt = in_array($tExtLower, ['jpg','jpeg','png','gif','webp','bmp','svg']))
                                                    @if((($tMime && str_starts_with($tMime, 'image/')) || $isImageExt) && method_exists($temp, 'temporaryUrl'))
                                                        <img src="{{ $temp->temporaryUrl() }}" class="lms-w-full lms-h-full lms-object-cover" alt="" />
                                                    @else
                                                        @php($tExt = strtoupper((string) pathinfo($tName, PATHINFO_EXTENSION)))
                                                        <div class="lms-w-full lms-h-full lms-grid lms-place-items-center lms-bg-gray-50">
                                                            <div class="lms-flex lms-items-center lms-gap-1">
                                                                <svg viewBox="0 0 24 24" class="lms-w-5 lms-h-5 lms-text-gray-500" fill="currentColor" aria-hidden="true">
                                                                    <path d="M6 2a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8.828a2 2 0 0 0-.586-1.414l-4.828-4.828A2 2 0 0 0 13.172 2H6zM14 3.414 19.586 9H15a1 1 0 0 1-1-1V3.414z"/>
                                                                </svg>
                                                                <span class="lms-text-[10px] lms-font-medium lms-text-gray-600">{{ $tExt ?: 'FILE' }}</span>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                                <button type="button" class="lms-absolute lms-top-1 lms-right-1 lms-inline-flex lms-items-center lms-justify-center lms-w-6 lms-h-6 lms-rounded-md lms-bg-white/90 lms-border hover:lms-bg-gray-100" title="{{ __('media-selector::messages.delete') }}" wire:click="removeUploadAt({{ $loop->index }})" aria-label="{{ __('media-selector::messages.delete') }}">
                                                    <svg viewBox="0 0 24 24" class="lms-w-3.5 lms-h-3.5 lms-text-red-600" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                        <polyline points="3 6 5 6 21 6"></polyline>
                                                        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                        <line x1="10" y1="11" x2="10" y2="17"></line>
                                                        <line x1="14" y1="11" x2="14" y2="17"></line>
                                                    </svg>
                                                </button>
                                                <div class="lms-mt-2 lms-text-[11px] lms-text-gray-600 lms-truncate lms-w-full">{{ $temp->getClientOriginalName() }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="lms-flex lms-items-center lms-gap-2 lms-ml-auto">
                                        <div class="lms-w-40 lms-h-2 lms-bg-gray-100 lms-rounded lms-overflow-hidden">
                                            <div class="lms-h-2 lms-bg-indigo-600" :style="`width: ${progress}%`"></div>
                                        </div>
                                        <button type="button" class="lms-px-3 lms-py-2 lms-text-sm lms-border lms-rounded-md" wire:click="clearUploads">{{ __('media-selector::messages.clear') }}</button>
                                        <button type="button" class="lms-px-3 lms-py-2 lms-text-sm lms-bg-black lms-text-white lms-rounded-md" wire:click="saveUpload">{{ __('media-selector::messages.save') }}</button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    @error('selection')
                        <div class="lms-px-6 lms-py-2 lms-text-sm lms-text-red-600">{{ $message }}</div>
                    @enderror

                    <div class="lms-px-6 lms-py-4 lms-border-t lms-flex lms-items-center lms-gap-4 lms-bg-gray-50">
                        <div class="lms-text-sm lms-text-gray-700">{{ __('media-selector::messages.files_selected', ['count' => count($selectedIds)]) }}</div>
                        <button type="button" class="lms-px-3 lms-py-2 lms-text-sm lms-border lms-rounded-md" wire:click="clearSelection">{{ __('media-selector::messages.clear') }}</button>
                        <div class="lms-ml-auto lms-flex lms-items-center lms-gap-2">
                            @if($media->currentPage() > 1)
                                <button type="button" class="lms-px-3 lms-py-2 lms-text-sm lms-border lms-rounded-md" wire:click="previousPage('lmsPage')">{{ __('media-selector::messages.prev') }}</button>
                            @endif
                            @if($media->hasMorePages())
                                <button type="button" class="lms-px-3 lms-py-2 lms-text-sm lms-border lms-rounded-md" wire:click="nextPage('lmsPage')">{{ __('media-selector::messages.next') }}</button>
                            @endif
                            @if(($canDelete ?? false) && $activeTab!=='trash')
                                <button type="button" class="lms-px-3 lms-py-2 lms-text-sm lms-rounded-md lms-border lms-text-red-700 lms-border-red-200" title="{{ __('media-selector::messages.delete_selected') }}" x-on:click.prevent="if(confirm(@js(__('media-selector::messages.delete_selected_q')))) $wire.deleteSelected()" {{ count($selectedIds) ? '' : 'disabled' }} aria-label="{{ __('media-selector::messages.delete_selected') }}">{{ __('media-selector::messages.delete') }}</button>
                            @endif
                            @if($activeTab==='trash' && $canRestoreTrash)
                                <button type="button" class="lms-px-3 lms-py-2 lms-text-sm lms-rounded-md lms-border lms-text-green-700 lms-border-green-200" x-on:click.prevent="if(confirm(@js(__('media-selector::messages.restore_selected_q')))) $wire.restoreSelected()" {{ count($selectedIds) ? '' : 'disabled' }}>{{ __('media-selector::messages.restore') }}</button>
                            @endif
                            @if($activeTab==='trash' && ($canDelete ?? false))
                                <button type="button" class="lms-px-3 lms-py-2 lms-text-sm lms-rounded-md lms-border lms-text-red-700 lms-border-red-200" title="{{ __('media-selector::messages.delete_selected_permanently') }}" x-on:click.prevent="if(confirm(@js(__('media-selector::messages.delete_permanently_q')))) $wire.forceDeleteSelected()" {{ count($selectedIds) ? '' : 'disabled' }} aria-label="{{ __('media-selector::messages.delete_selected_permanently') }}">{{ __('media-selector::messages.delete_permanently') }}</button>
                            @elseif($activeTab!=='trash')
                                <button type="button" class="lms-px-4 lms-py-2 lms-text-sm lms-rounded-md {{ count($selectedIds) ? 'lms-bg-indigo-700 lms-text-white hover:lms-bg-indigo-800' : 'lms-bg-gray-200 lms-text-gray-500 lms-cursor-not-allowed' }}" {{ count($selectedIds) ? '' : 'disabled' }} wire:click="confirmSelection">{{ __('media-selector::messages.insert') }}</button>
                            @endif
                            <button type="button" class="lms-px-3 lms-py-2 lms-text-sm lms-border lms-rounded-md" wire:click="closeModal">{{ __('media-selector::messages.close') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <div x-cloak x-show="toastVisible" x-transition class="lms-fixed lms-bottom-4 lms-right-4" style="z-index: 1201;">
        <div class="lms-px-3 lms-py-2 lms-bg-black lms-text-white lms-text-sm lms-rounded lms-shadow-lg" x-text="toastMessage"></div>
    </div>
</div>