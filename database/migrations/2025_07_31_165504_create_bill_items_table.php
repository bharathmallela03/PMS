<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillItemsTable extends Migration
{
    public function up()
    {
        Schema::create('bill_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pharmacist_id')->nullable()->constrained('pharmacists')->onDelete('set null');
            $table->foreignId('bill_id')->constrained()->onDelete('cascade');
            $table->foreignId('medicine_id')->constrained()->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->decimal('total', 10, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bill_items');
    }
}
