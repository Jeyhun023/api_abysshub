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
            $table->unsignedBigInteger('category_id');
            $table->string('name')->index();
            $table->string('slug');
            $table->longText('source_code');
            $table->longText('description');
            $table->decimal('price', 6, 2);
            $table->integer('rate')->default(0);
            $table->bigInteger('download_count')->default(0);
            $table->bigInteger('view_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('parent_id')->nullable()->references('id')->on('products');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('category_id')->references('id')->on('categories');
            
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
