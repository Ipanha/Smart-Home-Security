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
        Schema::create('auth_credentials', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role',['admin','home_owner','family_member']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auth_credentials');
    }
};
