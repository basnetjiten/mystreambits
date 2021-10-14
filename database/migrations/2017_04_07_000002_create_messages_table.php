<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('donator_id')->nullable();
            $table->float('amount', 10, 2);
            $table->float('commission', 10, 2)->default(0);
            $table->mediumText('message')->nullable();
            $table->string('name')->nullable();
            $table->string('billing_system')->nullable();
            $table->enum('status', ['wait', 'success', 'user'])->default('wait');
            $table->enum('invoice_status', ['paid', 'unpaid'])->default('unpaid');
            $table->enum('view_status', ['true', 'false'])->default('false');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messages');
    }
}
