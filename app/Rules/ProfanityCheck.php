<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Snipe\BanBuilder\CensorWords;
use App\Models\BadWord;

class ProfanityCheck implements Rule
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
        $censor = new CensorWords;
        $string = $censor->censorString($value);

        return !$string["isProfanity"];
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('messages.profanity');
    }
}
