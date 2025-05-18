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
        Schema::table('termination_participants', function (Blueprint $table) {
            $table->string('participant_jid')->nullable()->after('phone')->comment('WhatsApp participant JID (e.g., 554799740317@s.whatsapp.net)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('termination_participants', function (Blueprint $table) {
            $table->dropColumn('participant_jid');
        });
    }
}; 