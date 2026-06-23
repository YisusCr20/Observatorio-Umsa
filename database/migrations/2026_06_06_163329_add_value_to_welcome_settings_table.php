<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('welcome_settings')) {
            Schema::create('welcome_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->nullable();
                $table->text('value')->nullable();
                $table->timestamps();
            });

            return;
        }

        if (!Schema::hasColumn('welcome_settings', 'key')) {
            Schema::table('welcome_settings', function (Blueprint $table) {
                $table->string('key')->nullable()->after('id');
            });
        }

        if (!Schema::hasColumn('welcome_settings', 'value')) {
            Schema::table('welcome_settings', function (Blueprint $table) {
                $table->text('value')->nullable()->after('key');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('welcome_settings')) {
            Schema::table('welcome_settings', function (Blueprint $table) {
                if (Schema::hasColumn('welcome_settings', 'value')) {
                    $table->dropColumn('value');
                }

                if (Schema::hasColumn('welcome_settings', 'key')) {
                    $table->dropColumn('key');
                }
            });
        }
    }
};