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
        Schema::table('medical_records', function (Blueprint $table) {
            // Drop the existing foreign key
            $table->dropForeign(['doctor_id']);
            
            // Add the correct foreign key that references users table
            $table->foreign('doctor_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            // Drop the foreign key to users table
            $table->dropForeign(['doctor_id']);
            
            // Restore the original foreign key to doctors table
            $table->foreign('doctor_id')->references('id')->on('doctors')->onDelete('cascade');
        });
    }
}; 