<?php

namespace App\Models;

use App\Enums\ConsultationStatus;
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
        'clinic_id',
        'owner_id',
        'scheduled_date',
        'scheduled_time',
        'status',
        'fee',
        'notes'
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
        'status' => ConsultationStatus::class
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
        return $this->belongsTo(User::class, 'doctor_id')->where('role', UserRole::DOCTOR->value);
    }

    /**
     * Get the owner associated with the consultation.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
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
        return $query->where('status', ConsultationStatus::COMPLETED->value);
    }

    public function scopePending($query)
    {
        return $query->where('status', ConsultationStatus::PENDING->value);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', ConsultationStatus::IN_PROGRESS->value);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', ConsultationStatus::CANCELLED->value);
    }
} 