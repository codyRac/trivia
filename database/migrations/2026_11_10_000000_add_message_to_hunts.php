<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hunts', function (Blueprint $table) {
            // Optional note shown under the countdown on the holding page
            $table->text('message')->nullable()->after('intro');
        });
    }

    public function down(): void
    {
        Schema::table('hunts', function (Blueprint $table) {
            $table->dropColumn('message');
        });
    }
};
