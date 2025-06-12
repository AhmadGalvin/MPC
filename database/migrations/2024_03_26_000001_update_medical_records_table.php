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
        // Drop foreign key constraint first
        Schema::table('medical_records', function (Blueprint $table) {
            $table->dropForeign(['diagnosis_id']);
            $table->dropColumn('diagnosis_id');
            $table->dropColumn('file');
        });

        // Add new columns
        Schema::table('medical_records', function (Blueprint $table) {
            $table->text('diagnosis')->after('doctor_id');
            $table->text('treatment')->after('diagnosis');
            $table->text('notes')->nullable()->change();
            $table->date('next_visit_date')->nullable()->after('notes');
        });

        // Drop diagnoses table
        Schema::dropIfExists('diagnoses');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate diagnoses table
        Schema::create('diagnoses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->timestamps();
        });

        // Revert medical_records table changes
        Schema::table('medical_records', function (Blueprint $table) {
            $table->dropColumn(['diagnosis', 'treatment', 'next_visit_date']);
            $table->foreignId('diagnosis_id')->after('doctor_id')->constrained()->onDelete('cascade');
            $table->string('file')->nullable()->after('notes');
        });
    }
}; 