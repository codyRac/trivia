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
        Schema::create('flags', function (Blueprint $table) {
            $table->id();
            $table->string('code', 2)->unique(); // ISO 3166-1 alpha-2, lowercase
            $table->string('name');

            // Set when the flag is added to the collection (1 = first day's flag)
            $table->unsignedInteger('learned_order')->nullable()->unique();
            $table->date('learned_on')->nullable();

            $table->unsignedInteger('times_correct')->default(0);
            $table->unsignedInteger('times_wrong')->default(0);
            $table->timestamps();
        });

        // One row per day the flag game is played
        Schema::create('flag_days', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->foreignId('flag_id')->nullable()->constrained('flags'); // the new flag introduced that day (null once every flag is learned)
            $table->unsignedInteger('answered')->default(0);
            $table->unsignedInteger('correct')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flag_days');
        Schema::dropIfExists('flags');
    }
};
