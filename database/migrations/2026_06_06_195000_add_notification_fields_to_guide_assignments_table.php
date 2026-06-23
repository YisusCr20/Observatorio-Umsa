<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guide_assignments', function (Blueprint $table) {
            if (! Schema::hasColumn('guide_assignments', 'email_sent_at')) {
                $table->timestamp('email_sent_at')->nullable()->after('observacion');
            }

            if (! Schema::hasColumn('guide_assignments', 'whatsapp_link_generated_at')) {
                $table->timestamp('whatsapp_link_generated_at')->nullable()->after('email_sent_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('guide_assignments', function (Blueprint $table) {
            if (Schema::hasColumn('guide_assignments', 'whatsapp_link_generated_at')) {
                $table->dropColumn('whatsapp_link_generated_at');
            }

            if (Schema::hasColumn('guide_assignments', 'email_sent_at')) {
                $table->dropColumn('email_sent_at');
            }
        });
    }
};
