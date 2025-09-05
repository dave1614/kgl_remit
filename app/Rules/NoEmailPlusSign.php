<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NoEmailPlusSign implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // if (str_contains($value, '+')) {
        //     $fail('The :attribute must not contain a plus (+) sign.');
        // }

        // // Additional email format validation
        // if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
        //     $fail('The :attribute must be a valid email address.');
        // }
    }
}
