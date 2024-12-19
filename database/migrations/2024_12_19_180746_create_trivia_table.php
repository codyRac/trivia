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
        Schema::create('trivia', function (Blueprint $table) {
            $table->id();
            $table->longtext('question');
            $table->string('category');
            $table->string('answer');
            $table->string('wrong_1');
            $table->string('wrong_2');
            $table->string('wrong_3');
            $table->boolean('used')->default(0);
            $table->dateTime('used_on')->nullable();

            $table->timestamps();
            $table->SoftDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trivia');
    }
};
