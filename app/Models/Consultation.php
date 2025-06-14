<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Consultation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'pet_id',
        'doctor_id',
        'owner_id',
        'scheduled_date',
        'scheduled_time',
        'status',
        'payment_status',
        'payment_token',
        'payment_url',
        'payment_method',
        'transaction_id',
        'fee',
        'notes',
        'cancelled_at',
        'cancellation_reason',
        'completed_at',
        'rescheduled_at',
        'previous_schedule',
        'paid_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'scheduled_date' => 'date',
        'scheduled_time' => 'datetime',
        'fee' => 'decimal:2',
        'cancelled_at' => 'datetime',
        'completed_at' => 'datetime',
        'rescheduled_at' => 'datetime',
        'paid_at' => 'datetime',
        'previous_schedule' => 'array'
    ];

    /**
     * Get the pet associated with the consultation.
     */
    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    /**
     * Get the doctor associated with the consultation.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'doctor_id')->with('user');
    }

    /**
     * Get the owner associated with the consultation.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the chat messages for this consultation.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function medicalRecord(): HasOne
    {
        return $this->hasOne(MedicalRecord::class);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }
} 