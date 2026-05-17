<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('training_programs', function (Blueprint $table) {
            $table->string('skill_category', 20)->nullable()->after('category');
            $table->index('skill_category');
        });

        DB::statement(<<<'SQL'
            UPDATE training_programs
            SET skill_category = CASE
                WHEN LOWER(COALESCE(category, '')) LIKE '%soft%' THEN 'soft_skill'
                ELSE 'technical'
            END
            WHERE skill_category IS NULL
        SQL);
    }

    public function down(): void
    {
        Schema::table('training_programs', function (Blueprint $table) {
            if (Schema::hasColumn('training_programs', 'skill_category')) {
                $table->dropIndex(['skill_category']);
                $table->dropColumn('skill_category');
            }
        });
    }
};
