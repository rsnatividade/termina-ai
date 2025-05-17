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
        Schema::table('terminations', function (Blueprint $table) {
            $table->string('chosen_message')->nullable()->after('status');
            $table->string('scenario')->nullable()->after('chosen_message');
            $table->string('soundtrack')->nullable()->after('scenario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('terminations', function (Blueprint $table) {
            $table->dropColumn(['chosen_message', 'scenario', 'soundtrack']);
        });
    }
}; 