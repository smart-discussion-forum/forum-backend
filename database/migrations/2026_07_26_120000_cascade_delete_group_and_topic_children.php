<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * group_members.group_id, topics.group_id, and posts.topic_id were
     * created without cascadeOnDelete(), unlike messages.group_id and
     * participation_marks.group_id which already cascade. That meant
     * deleting a group failed with a foreign key constraint violation
     * (blocked by group_members first, then topics/posts) instead of
     * cleanly removing everything that belonged to it.
     */
    public function up(): void
    {
        Schema::table('group_members', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
            $table->foreign('group_id')->references('id')->on('groups')->cascadeOnDelete();
        });

        Schema::table('topics', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
            $table->foreign('group_id')->references('id')->on('groups')->cascadeOnDelete();
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['topic_id']);
            $table->foreign('topic_id')->references('id')->on('topics')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('group_members', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
            $table->foreign('group_id')->references('id')->on('groups');
        });

        Schema::table('topics', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
            $table->foreign('group_id')->references('id')->on('groups');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['topic_id']);
            $table->foreign('topic_id')->references('id')->on('topics');
        });
    }
};
