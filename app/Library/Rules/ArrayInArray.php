<?php

namespace App\Library\Rules;

use Illuminate\Contracts\Validation\Rule;

class ArrayInArray implements Rule
{
    protected array $errorValues = [];
    /** @var array */
    protected array $haystack;

    /**
     * ArrayInArray constructor.
     * @param array $haystack
     */
    public function __construct(array $haystack)
    {
        $this->haystack = $haystack;
    }

    public function passes($attribute, $values): bool
    {
        if (! is_array($values)) {
            return false;
        }
        $valid = true;
        foreach ($values as $v) {
            if (! in_array($v, $this->haystack, true)) {
                $valid               = false;
                if (is_string($v)) {
                    $this->errorValues[] = $v;
                }
            }
        }

        return $valid;
    }

    public function message()
    {
        return ':attributeの中に使用できない値['.implode(', ', $this->errorValues).']が含まれています。';
    }
}
