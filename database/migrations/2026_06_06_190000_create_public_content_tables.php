<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('page_sections')) {
            Schema::create('page_sections', function (Blueprint $table) {
                $table->id();
                $table->string('page');
                $table->string('section_key');
                $table->string('title')->nullable();
                $table->string('subtitle')->nullable();
                $table->text('body')->nullable();
                $table->string('image_path')->nullable();
                $table->unsignedInteger('position')->default(1);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->unique(['page', 'section_key']);
            });
        }

        if (! Schema::hasTable('gallery_images')) {
            Schema::create('gallery_images', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->text('description')->nullable();
                $table->string('image_path');
                $table->unsignedInteger('position')->default(1);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_images');
        Schema::dropIfExists('page_sections');
    }
};
