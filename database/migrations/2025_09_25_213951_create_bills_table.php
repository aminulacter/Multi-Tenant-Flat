<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('house_owner_id')->nullable();
            $table->foreign('house_owner_id')->references('id')->on('house_owners')->onDelete('set null')->onUpdate('cascade');
            $table->unsignedBigInteger('bill_category_id');
            $table->foreign('bill_category_id')->references('id')->on('bill_categories')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('flat_id');
            $table->foreign('flat_id')->references('id')->on('flats')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedTinyInteger('bill_month');
            $table->unsignedSmallInteger('bill_year');
            $table->date('bill_date');
            $table->enum('bill_status', ['pending', 'paid', 'overdue']);
            $table->string('bill_description')->nullable();
            $table->boolean('is_active');
            $table->boolean('is_paid');
            $table->decimal('amount', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
