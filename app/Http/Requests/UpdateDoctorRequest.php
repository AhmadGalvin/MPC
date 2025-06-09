<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\UserRole;
use Illuminate\Validation\Rule;

class UpdateDoctorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === UserRole::CLINIC_ADMIN;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->doctor->user_id),
            ],
            'specialization' => ['sometimes', 'required', 'string', 'max:255'],
            'sip_number' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('doctors')->ignore($this->doctor),
            ],
            'schedule' => ['sometimes', 'required', 'array'],
            'schedule.*.day' => ['required_with:schedule', 'string', 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday'],
            'schedule.*.start_time' => ['required_with:schedule', 'date_format:H:i'],
            'schedule.*.end_time' => ['required_with:schedule', 'date_format:H:i', 'after:schedule.*.start_time'],
        ];
    }
} 