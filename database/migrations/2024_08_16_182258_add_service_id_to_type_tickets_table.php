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
        Schema::table('type_tickets', function (Blueprint $table) {
            $table->unsignedBigInteger('service_id')->nullable()->after('id');

            $table->foreign('service_id')->references('id')->on('services')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('type_tickets', function (Blueprint $table) {
            // Supprime la clé étrangère et la colonne service_id
            $table->dropForeign(['service_id']);
            $table->dropColumn('service_id');
        });
                         }
};
