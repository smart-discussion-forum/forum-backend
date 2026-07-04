<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('warnings', function (Blueprint $table) {
        $table->id();
        $table->foreignId('User_id')
              ->constrained('users')
              ->onDelete('cascade');
        $table->string('Reason', 255);
        $table->dateTime('Issued_at');
    });
}

public function down(): void
{
    Schema::dropIfExists('warnings');
}
};
