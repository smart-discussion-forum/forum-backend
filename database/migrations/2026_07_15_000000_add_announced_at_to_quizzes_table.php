<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dateTime('announced_at')->nullable()->after('Duration');
        });

        $quizzes = DB::table('quizzes')->get(['quiz_id']);

        foreach ($quizzes as $quiz) {
            $cached = Cache::get('quiz_announced_' . $quiz->quiz_id);

            if ($cached) {
                DB::table('quizzes')
                    ->where('quiz_id', $quiz->quiz_id)
                    ->update(['announced_at' => $cached]);

                Cache::forget('quiz_announced_' . $quiz->quiz_id);
            }
        }
    }

    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropColumn('announced_at');
        });
    }
};
