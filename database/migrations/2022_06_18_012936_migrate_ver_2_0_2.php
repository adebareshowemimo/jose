<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MigrateVer202 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('bc_gig_orders')) {
            Schema::table('bc_gig_orders', function (Blueprint $table) {
                if (!Schema::hasColumn('bc_gig_orders', 'payout_id')) {
                    $table->bigInteger('payout_id')->nullable();
                }
            });
        }
        if(!Schema::hasTable('vendor_payouts'))
        {
            Schema::create('vendor_payouts', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('vendor_id')->index();
                $table->decimal('total',10,2)->nullable();
                $table->string('status',50)->nullable();
                $table->string("payout_method",50)->nullable();
                $table->text("account_info")->nullable();

                $table->smallInteger('month')->nullable();
                $table->smallInteger('year')->nullable();

                $table->text("note_to_admin")->nullable();
                $table->text("note_to_vendor")->nullable();
                $table->integer('last_process_by')->nullable();
                $table->timestamp("pay_date")->nullable();// admin pay date

                $table->integer('create_user')->nullable();
                $table->integer('update_user')->nullable();

                $table->index(['year','month']);

                $table->softDeletes();
                $table->timestamps();
            });
        }
        if(!Schema::hasTable('vendor_payout_accounts'))
        {
            Schema::create('vendor_payout_accounts', function (Blueprint $table) {
                $table->id();

                $table->bigInteger('vendor_id')->index();
                $table->string("payout_method",50)->nullable();
                $table->text("account_info")->nullable();

                $table->integer('create_user')->nullable();
                $table->integer('update_user')->nullable();

                $table->softDeletes();
                $table->timestamps();
            });
        }
        Schema::table('vendor_payout_accounts', function (Blueprint $table) {
            if (!Schema::hasColumn('vendor_payout_accounts', 'is_main')) {
                $table->tinyInteger('is_main')->nullable()->default(0);
            }
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vendor_payouts');
        Schema::dropIfExists('vendor_payout_accounts');
    }
}
