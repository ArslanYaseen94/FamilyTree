<?php

namespace App\Http\Requests\UserRequest;

use App\Support\MemberLifeDateValidator;
use Illuminate\Foundation\Http\FormRequest;

class MemberStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
        	'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'type' => 'required|integer',
            'gender' => 'required|integer',
            'birthdate' => 'nullable|date',
            'marriagedate' => 'nullable|date',
            'deathdate' => 'nullable|date',
            'user' => 'nullable|string|max:255',
            'poll_file' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'avatar' => 'nullable|integer',
            'facebook' => 'nullable|string|max:255',
            'twitter' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'tel' => 'nullable|string|max:15',
            'mobile' => 'nullable|string|max:15',
            'site' => 'nullable|url|max:255',
            'birthplace' => 'nullable|string|max:255',
            'deathplace' => 'nullable|string|max:255',
            'profession' => 'nullable|string',
            'company' => 'nullable|string',
            'interests' => 'nullable|string',
            'bio' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'file|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }

    public function withValidator($validator): void
    {
        MemberLifeDateValidator::addAfterRules($validator, $this);
    }
}
