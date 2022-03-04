<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('shop_id')->nullable()->unsigned();
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->longText('file')->nullable();
            $table->longText('description')->nullable();
            $table->decimal('price', 6, 2)->default(0)->nullable();
            $table->integer('rate')->default(0)->nullable();
            $table->bigInteger('download_count')->default(0)->nullable();
            $table->enum('status', [0, 1, 2, 3])->default(0);
            $table->json('tags')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('shop_id')->nullable()->references('id')->on('shops');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
