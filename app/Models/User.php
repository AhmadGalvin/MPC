<?php

namespace App\Models;

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
    ];

    /**
     * Get the clinic associated with the user.
     * Since we're using a single clinic system, this will always return the same clinic
     */
    public function clinic()
    {
        return Clinic::first();
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
     * @param string|array $role
     * @return bool
     */
    public function hasRole($role): bool
    {
        if (is_array($role)) {
            return in_array($this->role, $role);
        }
        return $this->role === $role;
    }

    /**
     * Check if the user is a clinic admin
     *
     * @return bool
     */
    public function isClinicAdmin(): bool
    {
        return $this->hasRole('clinic_admin');
    }

    /**
     * Check if the user is a doctor
     *
     * @return bool
     */
    public function isDoctor(): bool
    {
        return $this->role === 'doctor';
    }

    /**
     * Check if the user is an owner
     *
     * @return bool
     */
    public function isOwner(): bool
    {
        return $this->role === 'owner';
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

    public function doctorAppointments()
    {
        return $this->hasMany(Appointment::class, 'doctor_id');
    }

    public function ownerAppointments()
    {
        return $this->hasMany(Appointment::class, 'owner_id');
    }
}
