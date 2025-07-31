<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMedicinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('brand');
            $table->string('generic_name')->nullable();
            $table->string('category');
            $table->text('description')->nullable();
            $table->integer('quantity')->default(0);
            $table->decimal('price', 10, 2);
            $table->decimal('cost_price', 10, 2);
            $table->date('expiry_date');
            $table->string('batch_number');
            $table->string('photo')->nullable();
            $table->integer('minimum_stock')->default(50);
            $table->boolean('is_active')->default(true);
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('pharmacist_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['name', 'brand']);
            $table->index(['category', 'is_active']);
            $table->index(['pharmacist_id', 'is_active']);
            $table->index(['supplier_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('medicines');
    }
}