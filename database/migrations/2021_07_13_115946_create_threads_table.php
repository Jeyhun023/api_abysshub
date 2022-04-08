<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateThreadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('threads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('title', 255);
            $table->text('content');
            $table->string('description', 255);
            $table->json('tags');
            $table->string('slug', 300);
            
            $table->bigInteger('answer_count')->default(0);
            $table->bigInteger('comment_count')->default(0);
            $table->bigInteger('view_count')->default(0);
            $table->bigInteger('upvote')->default(0);
            $table->bigInteger('downvote')->default(0);
            $table->enum('type', [1, 2, 3]);

            $table->datetime('last_active_at');
            $table->datetime('closed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('threads');
    }
}
