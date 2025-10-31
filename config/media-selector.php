<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | The filesystem disk used to store and serve media items. This should
    | typically be the `public` disk or a cloud disk configured in your app.
    |
    */
    'disk' => env('MEDIA_SELECTOR_DISK', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Root Directory
    |--------------------------------------------------------------------------
    |
    | The root directory (within the selected disk) where uploaded files
    | will be stored. Do not include leading or trailing slashes.
    |
    */
    'directory' => env('MEDIA_SELECTOR_DIRECTORY', 'media'),

    /*
    |--------------------------------------------------------------------------
    | Database Table & Model
    |--------------------------------------------------------------------------
    |
    | The database table and Eloquent model used for media records. You may
    | publish the model stub and customize it as needed.
    |
    */
    'table' => env('MEDIA_SELECTOR_TABLE', 'media_selector_media'),
    'mediables_table' => env('MEDIA_SELECTOR_MEDIABLES_TABLE', 'media_selector_mediables'),
    'model' => \DrPshtiwan\LivewireMediaSelector\Models\Media::class,

    /*
    |--------------------------------------------------------------------------
    | Pagination & Upload Limits
    |--------------------------------------------------------------------------
    |
    | Configure how many items are listed per page and the maximum upload
    | size (in kilobytes) for each file.
    |
    */
    'per_page' => env('MEDIA_SELECTOR_PER_PAGE', 24),
    'max_upload_kb' => env('MEDIA_SELECTOR_MAX_UPLOAD_KB', 5120),

    /*
    |--------------------------------------------------------------------------
    | Allowed Extensions
    |--------------------------------------------------------------------------
    |
    | File extensions permitted for upload when you restrict by extensions.
    | If you instead pass explicit `mimes` to the component, those will take
    | precedence for validation and input `accept` filtering.
    |
    */
    'allowed_extensions' => [
        // images
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'svg',
        // documents
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv',
        // video
        'mp4', 'mov', 'webm', 'ogg',
        // audio
        'mp3', 'wav', 'm4a', 'aac', 'flac',
    ],

    /*
    |--------------------------------------------------------------------------
    | Allowed MIME Types
    |--------------------------------------------------------------------------
    |
    | MIME types permitted for upload or listing filters. Wildcards are
    | supported (e.g. image/*, video/*, audio/*). When explicit `mimes` are
    | provided to the component, they are used strictly for validation and
    | building the input `accept` attribute.
    |
    */
    'allowed_mimes' => [
        'image/*',
        'video/*',
        'audio/*',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'text/plain',
        'text/csv',
    ],

    /*
    |--------------------------------------------------------------------------
    | Multiple Selection
    |--------------------------------------------------------------------------
    |
    | Enable multiple selection mode by default. You can override this on a
    | per-component basis by passing :multiple="true|false".
    |
    */
    'multiple' => env('MEDIA_SELECTOR_MULTIPLE', false),

    /*
    |--------------------------------------------------------------------------
    | Visibility & Scoping
    |--------------------------------------------------------------------------
    |
    | Restrict listing to the current authenticated user's uploads, or apply
    | an extra query scope via a callable string like
    | "App\\Scopes\\MediaScope@apply" that receives ($query, $component).
    |
    */
    'restrict_to_current_user' => env('MEDIA_SELECTOR_RESTRICT_TO_USER', false),
    'extra_scope' => env('MEDIA_SELECTOR_EXTRA_SCOPE', null),

    /*
    |--------------------------------------------------------------------------
    | Deletion & Trash Access
    |--------------------------------------------------------------------------
    |
    | Control delete actions and access to the Trash tab. Component attributes
    | (e.g., :canDelete, :canSeeTrash, :canRestoreTrash) override these when
    | explicitly provided on the Livewire component.
    |
    */
    'can_delete' => env('MEDIA_SELECTOR_CAN_DELETE', false),
    'can_see_trash' => env('MEDIA_SELECTOR_CAN_SEE_TRASH', false),
    'can_restore_trash' => env('MEDIA_SELECTOR_CAN_RESTORE_TRASH', false),

    /*
    |--------------------------------------------------------------------------
    | Upload Permission
    |--------------------------------------------------------------------------
    |
    | Control whether the Upload tab and uploading are enabled. Can be
    | overridden per component via :canUpload="true|false".
    |
    */
    'can_upload' => env('MEDIA_SELECTOR_CAN_UPLOAD', true),

    /*
    |--------------------------------------------------------------------------
    | UI Framework
    |--------------------------------------------------------------------------
    |
    | Choose which UI flavor to render for the modal: 'tailwind' or 'bootstrap'.
    | You can override this per-component by passing :ui="'bootstrap'|'tailwind'".
    |
    */
    'ui' => env('MEDIA_SELECTOR_UI', 'tailwind'),

    /*
    |--------------------------------------------------------------------------
    | Preview MIME Types
    |--------------------------------------------------------------------------
    |
    | Controls which MIME types are eligible to render a visual thumbnail
    | in the library grid. Wildcards supported (e.g. image/*). Defaults
    | to images only.
    |
    */
    'preview_mimes' => [
        'image/*',
    ],
];
