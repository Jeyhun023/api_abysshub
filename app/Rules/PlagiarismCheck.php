<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PlagiarismCheck implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $url = "python C:/Users/User/Desktop/www/abyss-hub/public/python/copydetect/check.py 2>&1";

        return shell_exec( $url );
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('messages.plagiat');
    }
}
