<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('terminations', function (Blueprint $table) {
            $table->id();
            $table->string('owner_phone');
            $table->string('group_id')->nullable();
            $table->string('group_link')->nullable();
            $table->string('status')->default('waiting_friends');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('terminations');
    }
}; 