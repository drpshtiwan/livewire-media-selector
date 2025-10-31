<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableName = config('media-selector.table', 'media_selector_media');
        Schema::create($tableName, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('disk');
            $table->string('path');
            $table->string('filename');
            $table->string('collection')->nullable();
            $table->string('mime')->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index('filename');
            $table->index('collection');
            $table->index(['disk', 'path']);
            $table->index('created_at');
            $table->index('deleted_at');
        });
    }

    public function down(): void
    {
        $tableName = config('media-selector.table', 'media_selector_media');
        Schema::dropIfExists($tableName);
    }
};
