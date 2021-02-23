<?php

namespace Tests\ApiToSwaggerYamlPresenter\ConstantValues;

//use App\Http\Middleware\MemberAPI\ApiTokenAuth;

class Header
{
    public const BEARER_TOKEN_KEY = 'Authorization';
    public const API_TOKEN_KEY    = 'hoge-api-token';
    public const DESCRIPTIONS     = [
//        ApiTokenAuth::HEADER_KEY => 'APIを用いるためのトークン文字列',
    ];
}
