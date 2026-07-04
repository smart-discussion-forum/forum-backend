<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('blacklist', function (Blueprint $table) {
        $table->id();
        $table->foreignId('User_id')
              ->constrained('users')
              ->onDelete('cascade');
        $table->string('Reason', 255);
        $table->dateTime('Blacklisted_at');
        $table->dateTime('Expires_at')->nullable();
    });
}

public function down(): void
{
    Schema::dropIfExists('blacklist');
}
};
