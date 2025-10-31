<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $pivot = config('media-selector.mediables_table', 'media_selector_mediables');
        $mediaTable = config('media-selector.table', 'media_selector_media');
        Schema::create($pivot, function (Blueprint $table) use ($mediaTable, $pivot) {
            $table->id();
            $table->unsignedBigInteger('media_id');
            $table->string('mediable_type');
            $table->unsignedBigInteger('mediable_id');
            $table->string('collection')->nullable();
            $table->unsignedInteger('order_column')->nullable();
            $table->timestamps();

            $table->foreign('media_id')->references('id')->on($mediaTable)->onDelete('cascade');
            $table->index(['mediable_type', 'mediable_id']);
            $table->index('collection');
            $table->unique(['media_id', 'mediable_type', 'mediable_id', 'collection'], $pivot.'_unique');
        });
    }

    public function down(): void
    {
        $pivot = config('media-selector.mediables_table', 'media_selector_mediables');
        Schema::dropIfExists($pivot);
    }
};
