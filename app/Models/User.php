<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role' => UserRole::class,
    ];

    /**
     * Get the clinic associated with the user.
     */
    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class, 'clinic_id');
    }

    /**
     * Get the owned clinic associated with the user.
     */
    public function ownedClinic(): HasOne
    {
        return $this->hasOne(Clinic::class, 'owner_user_id');
    }

    /**
     * Get the doctor associated with the user.
     */
    public function doctor(): HasOne
    {
        return $this->hasOne(Doctor::class);
    }

    /**
     * Get the pets owned by the user.
     */
    public function pets(): HasMany
    {
        return $this->hasMany(Pet::class, 'owner_id');
    }

    /**
     * Check if the user has a specific role
     *
     * @param string $role
     * @return bool
     */
    public function hasRole($role): bool
    {
        if ($this->role instanceof UserRole) {
            if (is_array($role)) {
                return in_array($this->role->value, $role);
            }
            return $this->role->value === $role;
        }
        return false;
    }

    /**
     * Check if the user is a clinic admin
     *
     * @return bool
     */
    public function isClinicAdmin(): bool
    {
        return $this->hasRole(UserRole::CLINIC_ADMIN->value);
    }

    /**
     * Check if the user is a doctor
     *
     * @return bool
     */
    public function isDoctor(): bool
    {
        return $this->hasRole(UserRole::DOCTOR->value);
    }

    /**
     * Check if the user is an owner
     *
     * @return bool
     */
    public function isOwner(): bool
    {
        return $this->hasRole(UserRole::OWNER->value);
    }

    /**
     * Scope a query to only include users of a given role
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $role
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRole($query, $role)
    {
        if (is_array($role)) {
            return $query->whereIn('role', $role);
        }
        return $query->where('role', $role);
    }

    /**
     * Get the consultations where the user is the doctor
     */
    public function doctorConsultations(): HasMany
    {
        return $this->hasMany(Consultation::class, 'doctor_id');
    }

    /**
     * Get the medical records created by the user (doctor)
     */
    public function medicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class, 'doctor_id');
    }
}
