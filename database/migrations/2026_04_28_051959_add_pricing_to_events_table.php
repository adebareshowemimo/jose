<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->nullable()->after('description');
            $table->string('currency', 3)->nullable()->after('price');
            $table->unsignedInteger('capacity')->nullable()->after('currency');
            $table->unsignedInteger('seats_sold')->default(0)->after('capacity');
            $table->json('questions')->nullable()->after('seats_sold');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['price', 'currency', 'capacity', 'seats_sold', 'questions']);
        });
    }
};
