<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            $table->date('invoice_date')->nullable();
            $table->date('payment_date')->nullable();

            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('company_name')->nullable();
            $table->text('company_address')->nullable();
            $table->string('company_bank')->nullable();

            $table->unsignedBigInteger('client_id')->nullable();
            $table->string('client_name')->nullable();
            $table->text('client_address')->nullable();
            $table->string('client_bank')->nullable();

            $table->decimal('total_without_pvn', 10, 2)->default(0);
            $table->decimal('total_pvn', 10, 2)->default(0);
            $table->decimal('total_sum', 10, 2)->default(0);

            $table->string('status')->default('unpaid');

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
