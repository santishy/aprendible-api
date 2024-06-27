<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Slug implements ValidationRule
{
    protected string $message;
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->hasUnderscores($value)) {
            $this->message = __('validation.no_underscores', ["attribute" => "slug"]);
        }
        if ($this->startsWithDashes($value)) {
            $this->message = __('validation.no_starting_dashes', ["attribute" => "slug"]);
        }
        if ($this->endsWithDashes($value)) {
            $this->message = __('validation.no_ending_dashes', ["attribute" => "slug"]);
        }
        if (isset($this->message))
            $fail($this->message);
    }
    protected function hasUnderscores($value)
    {
        return preg_match('/_/', $value);
    }
    protected function startsWithDashes($value)
    {
        return preg_match('/^-/', $value);
    }
    protected function endsWithDashes($value)
    {
        return preg_match('/-$/', $value);
    }
}
