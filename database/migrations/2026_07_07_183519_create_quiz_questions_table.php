<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('quiz_questions', function (Blueprint $table) {
        $table->id('Question_id');
        $table->foreignId('quiz_id', )
              ->references('quiz_id')
              ->on('quizzes')
              ->onDelete('cascade');
        $table->text('Question');
        $table->text('Options')->nullable();
        $table->string('Correct_answer', 255);
        $table->integer('Marks');
    });
}
public function down(): void
{
    Schema::dropIfExists('quiz_questions');
}
};
