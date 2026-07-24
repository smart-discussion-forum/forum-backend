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
        Schema::table('topics', function (Blueprint $table) {
            $table->index('group_id', 'topics_group_id_index');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->index('topic_id', 'posts_topic_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('topics', function (Blueprint $table) {
            $table->dropIndex('topics_group_id_index');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex('posts_topic_id_index');
        });
    }
};
