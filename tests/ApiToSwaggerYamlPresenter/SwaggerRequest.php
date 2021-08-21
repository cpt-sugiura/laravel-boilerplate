<?php

namespace Tests\ApiToSwaggerYamlPresenter;

use App\Http\Requests\BaseFormRequest;

class SwaggerRequest
{
    public const GET     = 'GET';
    public const POST    = 'POST';
    public const PUT     = 'PUT';
    public const PATCH   = 'PATCH';
    public const DELETE  = 'DELETE';
    public const METHODS = [self::GET, self::POST, self::PUT, self::PATCH, self::DELETE];
    public string $method;
    public string $uri;
    public array $data;
    public array $headers;
    public ?BaseFormRequest $formRequest;

    public function __construct(string $method, string $uri, array $data, array $headers, BaseFormRequest $formRequest = null)
    {
        $this->method      = $method;
        $this->uri         = $uri;
        $this->data        = $data;
        $this->headers     = $headers;
        $this->formRequest = $formRequest;
    }
}
