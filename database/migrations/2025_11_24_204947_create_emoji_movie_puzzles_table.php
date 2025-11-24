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
        Schema::create('emoji_movie_puzzles', function (Blueprint $table) {
            $table->id();
            $table->string('emojis');

            // Correct movie title
            $table->string('correct_answer');

            // 6 wrong answers so we have 7 total choices
            $table->string('wrong_answer_1');
            $table->string('wrong_answer_2');
            $table->string('wrong_answer_3');
            $table->string('wrong_answer_4');
            $table->string('wrong_answer_5');
            $table->string( 'wrong_answer_6');

            // Date this puzzle should be used as the "daily" one
            $table->boolean('used')->nullable();
            $table->timestamps();
            $table->SoftDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emoji_movie_puzzles');
    }
};
