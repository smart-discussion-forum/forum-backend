<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('quizzes', function (Blueprint $table) {
        $table->id();
        $table->foreignId('lecturer_id')
              ->constrained('users')
              ->onDelete('cascade');
        $table->string('title', 150);
        $table->string('target_category', 100);
        $table->dateTime('Publish_time');
        $table->integer('duration_minutes');
        $table->string('status')->default('scheduled');
        $table->timestamps();
        });
    
}

public function down(): void
{
    Schema::dropIfExists('quizzes');
}
    
};
