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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('id')->constrained('roles')->nullOnDelete();
            $table->string('phone')->nullable()->after('email');
            $table->string('avatar')->nullable()->after('phone');
            $table->decimal('commission_rate', 5, 2)->nullable()->after('avatar');
            $table->enum('commission_type', ['fixed', 'percentage'])->nullable()->after('commission_rate');
            $table->boolean('is_verified')->default(false)->after('commission_type');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active')->after('is_verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn(['role_id', 'phone', 'avatar', 'commission_rate', 'commission_type', 'is_verified', 'status']);
        });
    }
};
