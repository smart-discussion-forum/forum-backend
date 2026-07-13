<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('quizzes', function (Blueprint $table) {
        $table->id('quiz_id');
        $table->foreignId('Lecturer_id')
              ->constrained('users')
              ->onDelete('cascade');
        $table->string('Title', 150);
        $table->string('Target_category', 100);
        $table->dateTime('Publish_time');
        $table->integer('Duration');
    });
}
public function down(): void
{
    Schema::dropIfExists('quizzes');
}
};
