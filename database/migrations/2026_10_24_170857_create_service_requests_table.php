<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
       public function up(): void
    {
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('technician_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('service_type');
            $table->string('unit_brand')->nullable();
            $table->string('unit_model')->nullable();
            $table->string('unit_location')->nullable();
            $table->string('service_address');
            $table->text('issue_description');
            $table->dateTime('preferred_at')->nullable();
            $table->dateTime('scheduled_at')->nullable();
            $table->string('status')->default('pending')->index();
            $table->string('priority')->default('normal');
            $table->text('staff_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('service_requests'); }
};
