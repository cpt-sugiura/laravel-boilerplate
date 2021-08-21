<?php

namespace {{ $namespace }};


class {{  $requestName }} extends {{ $baseClassName }}
{
    public function rules(): array
    {
        return array_key_camel({{ $modelName }}::createRules());
    }

    public function attributes(): array
    {
        return array_key_camel({{ $modelName }}::attributes());
    }
}
