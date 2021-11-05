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
            $table->bigInteger('parent_id')->nullable()->unsigned();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('category_id')->nullable()->unsigned();
            $table->unsignedBigInteger('shop_id')->nullable()->unsigned();
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->string('file')->nullable();
            $table->longText('description')->nullable();
            $table->decimal('price', 6, 2)->nullable();
            $table->integer('rate')->default(0)->nullable();
            $table->bigInteger('download_count')->default(0)->nullable();
            $table->bigInteger('view_count')->default(0)->nullable();
            $table->enum('status', [0, 1])->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('parent_id')->nullable()->references('id')->on('products');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('category_id')->nullable()->references('id')->on('categories');
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
