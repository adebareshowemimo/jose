<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            $this->rebuildSqliteTable(['draft', 'pending', 'active', 'paused', 'closed', 'expired']);
            return;
        }

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE job_listings MODIFY status ENUM('draft','pending','active','paused','closed','expired') NOT NULL DEFAULT 'draft'");
        }
    }

    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            DB::table('job_listings')->where('status', 'pending')->update(['status' => 'draft']);
            $this->rebuildSqliteTable(['draft', 'active', 'paused', 'closed', 'expired']);
            return;
        }

        if ($driver === 'mysql') {
            DB::table('job_listings')->where('status', 'pending')->update(['status' => 'draft']);
            DB::statement("ALTER TABLE job_listings MODIFY status ENUM('draft','active','paused','closed','expired') NOT NULL DEFAULT 'draft'");
        }
    }

    private function rebuildSqliteTable(array $statuses): void
    {
        Schema::disableForeignKeyConstraints();

        if (Schema::hasTable('job_listings_old')) {
            if (Schema::hasTable('job_listings')) {
                Schema::drop('job_listings');
            }
            Schema::rename('job_listings_old', 'job_listings');
        }

        Schema::rename('job_listings', 'job_listings_old');

        Schema::create('job_listings', function (Blueprint $table) use ($statuses) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('posted_by')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->text('description');
            $table->string('qualification')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('job_type_id')->nullable()->constrained('job_types')->nullOnDelete();
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->string('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('salary_min', 12, 2)->nullable();
            $table->decimal('salary_max', 12, 2)->nullable();
            $table->enum('salary_type', ['hourly', 'monthly', 'yearly'])->nullable();
            $table->string('experience_required')->nullable();
            $table->enum('gender_preference', ['any', 'male', 'female'])->default('any');
            $table->date('deadline')->nullable();
            $table->enum('apply_method', ['internal', 'external_link', 'email'])->default('internal');
            $table->string('apply_url')->nullable();
            $table->string('apply_email')->nullable();
            $table->unsignedInteger('vacancies')->nullable();
            $table->string('thumbnail')->nullable();
            $table->json('gallery')->nullable();
            $table->string('video_url')->nullable();
            $table->string('hours')->nullable();
            $table->enum('hours_type', ['full-time', 'part-time'])->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_urgent')->default(false);
            $table->boolean('is_approved')->default(false);
            $table->enum('status', $statuses)->default('draft');
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement('
            INSERT INTO job_listings (
                id, company_id, posted_by, title, slug, description, qualification, category_id, job_type_id,
                location_id, address, latitude, longitude, salary_min, salary_max, salary_type, experience_required,
                gender_preference, deadline, apply_method, apply_url, apply_email, vacancies, thumbnail, gallery,
                video_url, hours, hours_type, is_featured, is_urgent, is_approved, status, created_at, updated_at, deleted_at
            )
            SELECT
                id, company_id, posted_by, title, slug, description, qualification, category_id, job_type_id,
                location_id, address, latitude, longitude, salary_min, salary_max, salary_type, experience_required,
                gender_preference, deadline, apply_method, apply_url, apply_email, vacancies, thumbnail, gallery,
                video_url, hours, hours_type, is_featured, is_urgent, is_approved, status, created_at, updated_at, deleted_at
            FROM job_listings_old
        ');

        Schema::drop('job_listings_old');
        Schema::table('job_listings', function (Blueprint $table) {
            $table->unique('slug');
        });
        Schema::enableForeignKeyConstraints();
    }
};
