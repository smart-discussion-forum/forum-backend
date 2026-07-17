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
        Schema::create('quiz_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->references('Attempt_id')->on('quiz_attempts')->onDelete('cascade');
            $table->foreignId('question_id')->references('Question_id')->on('quiz_questions')->onDelete('cascade');
            $table->string('submitted_answer');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
        });
    }


};
