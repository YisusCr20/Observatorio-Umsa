<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('public_content_items')) {
            Schema::create('public_content_items', function (Blueprint $table) {
                $table->id();
                $table->string('page');
                $table->string('title');
                $table->string('category')->nullable();
                $table->date('event_date')->nullable();
                $table->text('body')->nullable();
                $table->string('image_path')->nullable();
                $table->string('button_label')->nullable();
                $table->string('button_url')->nullable();
                $table->unsignedInteger('position')->default(1);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('public_content_items');
    }
};
