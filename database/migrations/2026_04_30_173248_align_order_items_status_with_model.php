<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            // SQLite stores enums as CHECK constraints; we must rebuild the table to alter them.
            $this->rebuildSqlite([
                'pending', 'fulfilled', 'failed', 'cancelled', 'refunded',
            ]);
        } else {
            // MySQL/MariaDB/Postgres: drop the constraint by widening to a plain string.
            // The model (App\Models\OrderItem) is the source of truth for valid values.
            Schema::table('order_items', function ($table) {
                $table->string('status', 32)->default('pending')->change();
            });
        }
    }

    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            $this->rebuildSqlite([
                'pending', 'completed', 'cancelled', 'refunded',
            ]);
        } else {
            // No reliable rollback — re-tightening could fail if rows now hold the new values.
            Schema::table('order_items', function ($table) {
                $table->string('status', 32)->default('pending')->change();
            });
        }
    }

    protected function rebuildSqlite(array $allowed): void
    {
        $allowedList = "'" . implode("','", $allowed) . "'";

        DB::statement('PRAGMA foreign_keys = OFF');

        try {
            DB::statement(<<<SQL
                CREATE TABLE order_items_new (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    order_id INTEGER NOT NULL,
                    orderable_type VARCHAR NOT NULL,
                    orderable_id INTEGER NOT NULL,
                    price NUMERIC NOT NULL,
                    quantity INTEGER NOT NULL DEFAULT 1,
                    subtotal NUMERIC NOT NULL,
                    status VARCHAR CHECK (status IN ($allowedList)) NOT NULL DEFAULT 'pending',
                    meta TEXT,
                    created_at DATETIME,
                    updated_at DATETIME,
                    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
                )
            SQL);

            DB::statement('INSERT INTO order_items_new (id, order_id, orderable_type, orderable_id, price, quantity, subtotal, status, meta, created_at, updated_at) SELECT id, order_id, orderable_type, orderable_id, price, quantity, subtotal, status, meta, created_at, updated_at FROM order_items');

            DB::statement('DROP TABLE order_items');
            DB::statement('ALTER TABLE order_items_new RENAME TO order_items');
            DB::statement('CREATE INDEX order_items_orderable_type_orderable_id_index ON order_items(orderable_type, orderable_id)');
            DB::statement('CREATE INDEX order_items_order_id_index ON order_items(order_id)');
        } finally {
            DB::statement('PRAGMA foreign_keys = ON');
        }
    }
};
