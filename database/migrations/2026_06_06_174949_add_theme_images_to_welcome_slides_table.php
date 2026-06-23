<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('welcome_slides', function (Blueprint $table) {
            $table->string('image_dark_path')->nullable()->after('image_path');
            $table->string('image_light_path')->nullable()->after('image_dark_path');
        });
    }

    public function down(): void
    {
        Schema::table('welcome_slides', function (Blueprint $table) {
            $table->dropColumn([
                'image_dark_path',
                'image_light_path',
            ]);
        });
    }
};