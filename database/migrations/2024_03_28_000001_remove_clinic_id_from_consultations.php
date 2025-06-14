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
        Schema::disableForeignKeyConstraints();
        
        Schema::table('consultations', function (Blueprint $table) {
            if (Schema::hasColumn('consultations', 'clinic_id')) {
                $table->dropForeign(['clinic_id']);
                $table->dropColumn('clinic_id');
            }
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            if (!Schema::hasColumn('consultations', 'clinic_id')) {
                $table->foreignId('clinic_id')->after('owner_id')->constrained()->onDelete('cascade');
            }
        });
    }
}; 