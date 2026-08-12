<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The site is single-currency: the active currency comes from the
 * `currency.default` setting, resolved in PHP via App\Support\Currency::default().
 *
 * These columns were created with a hardcoded 'USD' default, which meant any
 * insert that omitted `currency` was silently stamped USD regardless of the
 * configured site currency. The application layer now always supplies the
 * currency explicitly, so the schema-level default is removed to stop it
 * acting as a second, conflicting source of truth.
 *
 * Amount columns are deliberately untouched — no stored value is converted.
 */
return new class extends Migration
{
    /**
     * table => currency column
     */
    private array $targets = [
        'orders'               => 'currency',
        'payments'             => 'currency',
        'payouts'              => 'currency',
        'receipts'             => 'currency',
        'training_programs'    => 'currency',
        'candidate_boosts'     => 'currency',
        'events'               => 'currency',
        'recruitment_requests' => 'salary_currency',
    ];

    public function up(): void
    {
        foreach ($this->targets as $table => $column) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($column) {
                $blueprint->string($column, 3)->nullable()->default(null)->change();
            });
        }
    }

    public function down(): void
    {
        foreach ($this->targets as $table => $column) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($column) {
                $blueprint->string($column, 3)->default('USD')->change();
            });
        }
    }
};
