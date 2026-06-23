<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('welcome_slides', function (Blueprint $table) {
        $table->id();
        $table->string('title_highlight');
        $table->string('title_normal');
        $table->text('description')->nullable();
        $table->string('image_path')->nullable();
        $table->string('image_shape')->default('rounded');
        $table->unsignedInteger('position')->default(1);
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('welcome_slides');
    }
};
