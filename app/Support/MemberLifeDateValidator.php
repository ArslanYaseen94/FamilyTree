<?php

namespace App\Support;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;

class MemberLifeDateValidator
{
    public static function addAfterRules(Validator $validator, Request $request): void
    {
        $validator->after(function (Validator $v) use ($request) {
            self::validate($v, $request);
        });
    }

    public static function validate(Validator $validator, Request $request): void
    {
        $isAlive = $request->has('death');
        $birth = $request->input('birthdate');
        $death = $request->input('deathdate');

        if ($isAlive && !empty($death)) {
            $validator->errors()->add(
                'deathdate',
                __('messages.Death date cannot be entered while the member is alive.')
            );
        }

        if (!$isAlive && empty($death)) {
            $validator->errors()->add(
                'deathdate',
                __('messages.Death date is required when the member is deceased.')
            );
        }

        if ($birth && $death) {
            if ($birth === $death) {
                $validator->errors()->add(
                    'deathdate',
                    __('messages.Birth date and death date cannot be the same.')
                );
            } elseif ($death < $birth) {
                $validator->errors()->add(
                    'deathdate',
                    __('messages.Death date must be after birth date.')
                );
            }
        }
    }

    /** Clear death date when member is marked alive. */
    public static function normalizeDates(Request $request): array
    {
        $isAlive = $request->has('death');

        return [
            'death' => $isAlive ? 1 : 0,
            'deathdate' => $isAlive ? null : $request->input('deathdate'),
        ];
    }
}
