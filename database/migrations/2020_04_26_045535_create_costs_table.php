<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('costs', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->unsignedBigInteger('modifier_user_id');
            $table->string('name', 128)->unique();
            $table->string('comment', 128)->nullable();
            $table->string('price', 19);
            $table->tinyInteger('currency');

            $table->foreign('modifier_user_id')
                ->references('id')
                ->on('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('costs');
    }
}
