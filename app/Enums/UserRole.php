<?php

namespace App\Enums;

enum UserRole: string
{
    case OWNER = 'owner';
    case DOCTOR = 'doctor';
    case CLINIC_ADMIN = 'clinic_admin';
} 