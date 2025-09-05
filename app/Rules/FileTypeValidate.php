<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class FileTypeValidate implements ValidationRule
{
    protected array $extensions;

    /**
     * Create a new rule instance.
     */
    public function __construct(array $extensions)
    {
        $this->extensions = $extensions;
    }

    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! in_array($value->getClientOriginalExtension(), $this->extensions)) {
            $fail("The {$attribute} file type is not supported.");
        }
    }
}
