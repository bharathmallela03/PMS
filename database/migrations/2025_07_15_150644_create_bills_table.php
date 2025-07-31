<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->string('bill_number')->unique();
            $table->string('patient_name');
            $table->string('patient_phone')->nullable();
            $table->text('patient_address')->nullable();
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', ['pending', 'paid', 'overdue'])->default('pending');
            $table->foreignId('pharmacist_id')->nullable()->constrained('pharmacists')->onDelete('set null');
            $table->text('notes')->nullable();
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
        Schema::dropIfExists('bills');
    }
}
