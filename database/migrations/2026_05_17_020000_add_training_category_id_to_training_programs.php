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
            $table->foreignId('training_category_id')
                ->nullable()
                ->after('category')
                ->constrained('training_categories')
                ->nullOnDelete();
        });

        if (Schema::hasColumn('training_programs', 'skill_category')) {
            $softId = DB::table('training_categories')->where('slug', 'soft-skills')->value('id');
            $techId = DB::table('training_categories')->where('slug', 'technical')->value('id');

            if ($softId) {
                DB::table('training_programs')
                    ->where('skill_category', 'soft_skill')
                    ->update(['training_category_id' => $softId]);
            }
            if ($techId) {
                DB::table('training_programs')
                    ->where('skill_category', 'technical')
                    ->update(['training_category_id' => $techId]);
            }

            Schema::table('training_programs', function (Blueprint $table) {
                $table->dropIndex(['skill_category']);
                $table->dropColumn('skill_category');
            });
        }
    }

    public function down(): void
    {
        Schema::table('training_programs', function (Blueprint $table) {
            if (! Schema::hasColumn('training_programs', 'skill_category')) {
                $table->string('skill_category', 20)->nullable()->after('category');
                $table->index('skill_category');
            }
        });

        if (Schema::hasColumn('training_programs', 'training_category_id')) {
            $softId = DB::table('training_categories')->where('slug', 'soft-skills')->value('id');
            $techId = DB::table('training_categories')->where('slug', 'technical')->value('id');

            if ($softId) {
                DB::table('training_programs')
                    ->where('training_category_id', $softId)
                    ->update(['skill_category' => 'soft_skill']);
            }
            if ($techId) {
                DB::table('training_programs')
                    ->where('training_category_id', $techId)
                    ->update(['skill_category' => 'technical']);
            }

            Schema::table('training_programs', function (Blueprint $table) {
                $table->dropConstrainedForeignId('training_category_id');
            });
        }
    }
};
