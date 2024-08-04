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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('sujet', 100);
            $table->text('description');
            $table->string('image')->nullable();
            $table->string('status')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users', 'id');
            $table->foreignId('client_id')->nullable()->constrained('clients', 'id');
            $table->foreignId('service_id')->constrained('services', 'id');
            $table->foreignId('type_ticket_id')->constrained('type_tickets', 'id');
            $table->foreignId('priorite_id')->constrained('priorites', 'id');
            $table->string('work_description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
