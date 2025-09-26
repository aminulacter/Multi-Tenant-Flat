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
        Schema::create('flats', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('building_id');
            $table->foreign('building_id')->references('id')->on('buildings')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('set null')->onUpdate('cascade');
            $table->unsignedBigInteger('house_owner_id')->nullable();
            $table->foreign('house_owner_id')->references('id')->on('house_owners')->onDelete('set null')->onUpdate('cascade');
            $table->unique(['name', 'building_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flats');
    }
};
