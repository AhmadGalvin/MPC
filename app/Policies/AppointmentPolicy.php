<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AppointmentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'doctor' || $user->role === 'owner';
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Appointment $appointment): bool
    {
        if ($user->role === 'doctor') {
            return $user->id === $appointment->doctor_id;
        }
        
        if ($user->role === 'owner') {
            return $user->id === $appointment->owner_id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === 'owner';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Appointment $appointment): bool
    {
        if ($user->role === 'doctor') {
            return $user->id === $appointment->doctor_id;
        }
        
        if ($user->role === 'owner') {
            return $user->id === $appointment->owner_id && $appointment->status === 'pending';
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Appointment $appointment): bool
    {
        if ($user->role === 'owner') {
            return $user->id === $appointment->owner_id && $appointment->status === 'pending';
        }

        return false;
    }
} 