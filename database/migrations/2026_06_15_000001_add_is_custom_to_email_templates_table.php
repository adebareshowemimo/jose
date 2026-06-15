<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('email_templates', function (Blueprint $table) {
            // Marks templates an admin created or cloned (vs. the seeded system set).
            // Custom templates are always offered in the recruitment-request notify modal.
            $table->boolean('is_custom')->default(false)->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('email_templates', function (Blueprint $table) {
            $table->dropColumn('is_custom');
        });
    }
};
