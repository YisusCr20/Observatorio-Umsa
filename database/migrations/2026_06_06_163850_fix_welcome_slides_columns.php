<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('welcome_slides')) {
            Schema::create('welcome_slides', function (Blueprint $table) {
                $table->id();
                $table->string('title_highlight')->nullable();
                $table->string('title_normal')->nullable();
                $table->text('description')->nullable();
                $table->string('image_path')->nullable();
                $table->string('image_shape')->default('rounded');
                $table->integer('position')->default(1);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });

            return;
        }

        Schema::table('welcome_slides', function (Blueprint $table) {
            if (!Schema::hasColumn('welcome_slides', 'title_highlight')) {
                $table->string('title_highlight')->nullable();
            }

            if (!Schema::hasColumn('welcome_slides', 'title_normal')) {
                $table->string('title_normal')->nullable();
            }

            if (!Schema::hasColumn('welcome_slides', 'description')) {
                $table->text('description')->nullable();
            }

            if (!Schema::hasColumn('welcome_slides', 'image_path')) {
                $table->string('image_path')->nullable();
            }

            if (!Schema::hasColumn('welcome_slides', 'image_shape')) {
                $table->string('image_shape')->default('rounded');
            }

            if (!Schema::hasColumn('welcome_slides', 'position')) {
                $table->integer('position')->default(1);
            }

            if (!Schema::hasColumn('welcome_slides', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
        });
    }

    public function down(): void
    {
        Schema::table('welcome_slides', function (Blueprint $table) {
            foreach ([
                'title_highlight',
                'title_normal',
                'description',
                'image_path',
                'image_shape',
                'position',
                'is_active',
            ] as $column) {
                if (Schema::hasColumn('welcome_slides', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};