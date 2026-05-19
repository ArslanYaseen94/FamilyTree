<?php

namespace App\Http\Requests\UserRequest;

use App\Support\MemberLifeDateValidator;
use Illuminate\Foundation\Http\FormRequest;

class MemberUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'type' => 'nullable|integer',
            'gender' => 'nullable|integer',
            'birthdate' => 'nullable|date',
            'marriagedate' => 'nullable|date',
            'deathdate' => 'nullable|date',
            'email' => 'nullable|email|max:255',
        ];
    }

    public function withValidator($validator): void
    {
        MemberLifeDateValidator::addAfterRules($validator, $this);
    }
}
