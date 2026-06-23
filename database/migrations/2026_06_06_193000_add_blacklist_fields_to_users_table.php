<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'is_blacklisted')) {
                $table->boolean('is_blacklisted')->default(false)->after('role');
            }

            if (! Schema::hasColumn('users', 'blacklist_reason')) {
                $table->text('blacklist_reason')->nullable()->after('is_blacklisted');
            }

            if (! Schema::hasColumn('users', 'blacklisted_at')) {
                $table->timestamp('blacklisted_at')->nullable()->after('blacklist_reason');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'blacklisted_at')) {
                $table->dropColumn('blacklisted_at');
            }

            if (Schema::hasColumn('users', 'blacklist_reason')) {
                $table->dropColumn('blacklist_reason');
            }

            if (Schema::hasColumn('users', 'is_blacklisted')) {
                $table->dropColumn('is_blacklisted');
            }
        });
    }
};
