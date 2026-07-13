<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('quiz_attempts', function (Blueprint $table) {
        $table->id('Attempt_id');
        $table->foreignId('quiz_id')
              ->references('quiz_id')
              ->on('quizzes')
              ->onDelete('cascade');
        $table->foreignId('Student_id')
              ->constrained('users')
              ->onDelete('cascade');
        $table->decimal('Score', 5, 2);
        $table->boolean('Auto_submitted')->default(false);
    });
}
public function down(): void
{
    Schema::dropIfExists('quiz_attempts');
}
};
