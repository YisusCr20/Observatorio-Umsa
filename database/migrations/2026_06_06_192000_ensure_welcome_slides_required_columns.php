<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('welcome_slides')) {
            return;
        }

        Schema::table('welcome_slides', function (Blueprint $table) {
            if (! Schema::hasColumn('welcome_slides', 'image_shape')) {
                $table->string('image_shape')->default('rounded')->after('image_path');
            }

            if (! Schema::hasColumn('welcome_slides', 'position')) {
                $table->unsignedInteger('position')->default(1)->after('image_shape');
            }

            if (! Schema::hasColumn('welcome_slides', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('position');
            }
        });
    }

    public function down(): void
    {
        //
    }
};
