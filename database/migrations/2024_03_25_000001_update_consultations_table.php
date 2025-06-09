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
        Schema::table('consultations', function (Blueprint $table) {
            $table->date('scheduled_date')->after('status')->nullable();
            $table->time('scheduled_time')->after('scheduled_date')->nullable();
            $table->decimal('fee', 10, 2)->nullable()->after('scheduled_time');
            $table->text('notes')->nullable()->after('fee');
            $table->foreignId('clinic_id')->after('owner_id')->constrained()->onDelete('cascade');
            $table->timestamp('cancelled_at')->nullable()->after('notes');
            $table->string('cancellation_reason')->nullable()->after('cancelled_at');
            $table->timestamp('completed_at')->nullable()->after('cancellation_reason');
            $table->timestamp('rescheduled_at')->nullable()->after('completed_at');
            $table->json('previous_schedule')->nullable()->after('rescheduled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            $table->dropForeign(['clinic_id']);
            $table->dropColumn([
                'scheduled_date',
                'scheduled_time',
                'fee',
                'notes',
                'clinic_id',
                'cancelled_at',
                'cancellation_reason',
                'completed_at',
                'rescheduled_at',
                'previous_schedule'
            ]);
        });
    }
}; 