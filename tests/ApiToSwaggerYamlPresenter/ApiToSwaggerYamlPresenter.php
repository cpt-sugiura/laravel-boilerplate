<?php

namespace Tests\ApiToSwaggerYamlPresenter;

use Arr;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Http\Testing\File;
use Str;
use Tests\ApiToSwaggerYamlPresenter\ConstantValues\Header;
use Tests\ApiToSwaggerYamlPresenter\ConstantValues\Tag;
use Tests\ApiToSwaggerYamlPresenter\ConstantValues\Url;

class ApiToSwaggerYamlPresenter
{
    /**
     * @var SwaggerRequest
     */
    protected SwaggerRequest $request;
    /**
     * @var TestResponse
     */
    protected TestResponse $response;
    /**
     * @var array
     */
    protected array $baseYaml;

    /**
     * ApiToYamlPresenter constructor.
     * @param SwaggerRequest $request
     * @param TestResponse   $response
     * @param array          $baseYaml
     */
    public function __construct(SwaggerRequest $request, TestResponse $response, array $baseYaml = [])
    {
        $this->request  = $request;
        $this->response = $response;
        $this->baseYaml = $baseYaml;
    }

    public function toArray(): array
    {
        $yaml                = $this->baseYaml;
        $yaml['paths']       = is_array($yaml['paths']) ? $yaml['paths'] : [];
        [$url, $summary]     = Url::getUrl($this->request->uri);
        $yaml['paths'][$url] = $yaml['paths'][$url] ?? [];

        $method                       = Str::lower($this->request->method);
        $yaml['paths'][$url][$method] = $yaml['paths'][$url][$method] ?? [];

        $yaml['paths'][$url][$method]['summary']  = $summary;
        $yaml['paths'][$url][$method]['tags']     = $this->getTags();
        if (! empty($secure = $this->getSecure())) {
            $yaml['paths'][$url][$method]['security'] = $secure;
        }

        if (! empty($params = $this->getParams())) {
            $yaml['paths'][$url][$method]['parameters'] = $params;
        }
        if (! empty($request = $this->getRequestBody()) && strtoupper($method) !== strtoupper(SwaggerRequest::GET)) {
            $yaml['paths'][$url][$method]['requestBody'] = $request;
        }

        $yaml['paths'][$url][$method]['responses'] = $yaml['paths'][$url][$method]['responses'] ?? [];
        if ($this->hasResponseMethod('status')) {
            $status                                             = $this->response->status();
            $yaml['paths'][$url][$method]['responses'][$status] = [
                'description'=> '',
                'content'    => ['application/json' => ['schema' => $status === 404 ? null : $this->makeResponseSchema()]]
            ];
        }

        ksort($yaml['paths']);

        return $yaml;
    }

    private function makeResponseSchema(): array
    {
        $decodedContent = json_decode($this->response->content(), true, 1024, JSON_THROW_ON_ERROR);

        return $this->makeSchema($decodedContent);
    }

    /**
     * @param $value
     * @return array
     */
    protected static function convertToProperty($value): array
    {
        if (! is_array($value) && ! is_object($value)) {
            return [
                'type'    => gettype($value),
                'example' => $value,
            ];
        }

        $arrValue = [];
        foreach ($value as $index => $childValue) {
            $arrValue[$index] =  self::convertToProperty($childValue);
        }
        if (is_array($value) && array_values($value) === $value) {
            return [
                'type'  => 'array',
                'items' => Arr::first($arrValue), // swaggerYamlの例には唯一つあるのみにしなければならない
            ];
        }

        return [
            'type'       => 'object',
            'properties' => $arrValue,
        ];
    }

    protected function getParams(): array
    {
        $yamlRequest = [];
        $yamlRequest = $this->getRequestHeader($yamlRequest);
        $yamlRequest = $this->getRequestPath($yamlRequest);
        if ($this->request->method === SwaggerRequest::GET && ! empty($query = $this->getRequestQuery())) {
            $yamlRequest[] = $query;
        }

        return $yamlRequest;
    }

    private function hasResponseMethod(string $methodName): bool
    {
        return method_exists($this->response, $methodName)
            || method_exists($this->response->baseResponse, $methodName);
    }

    /**
     * セキュリティ以外のヘッダ部のパースと変換
     * @param  array $yamlRequest
     * @return array
     */
    protected function getRequestHeader(array $yamlRequest): array
    {
        foreach ($this->request->headers as $name => $header) {
            if (in_array($name, [Header::BEARER_TOKEN_KEY, Header::API_TOKEN_KEY], true)) {
                continue;
            }
            $swaggerFormat                = [];
            $swaggerFormat['name']        = $name;
            $swaggerFormat['in']          = 'header';
            $swaggerFormat['required']    = true;
            $swaggerFormat['description'] = Header::DESCRIPTIONS[$name];
            $yamlRequest[]                = $swaggerFormat;
        }

        return $yamlRequest;
    }

    /** セキュリティ部のパースと変換 */
    protected function getSecure(): array
    {
        $secure = [];
        if (array_key_exists(Header::BEARER_TOKEN_KEY, $this->request->headers)) {
            $secure[] = ['Bearer' => []];
        }
        if (array_key_exists(Header::API_TOKEN_KEY, $this->request->headers)) {
            $secure[] = ['XApiToken' => []];
        }

        return $secure;
    }

    private function getRequestBody(): array
    {
        $contentType = 'application/json';
        $properties  = [];
        foreach ($this->request->data as $name => $bodyData) {
            if ($bodyData instanceof File) {
                // ファイルの場合
                $contentType       = 'multipart/form-data';
                $properties[$name] = ['type'   => 'string', 'format' => 'binary', 'description' => $this->request->formRequest ? $this->request->formRequest->getRuleDescription($name) : $name];
            } elseif (isset($bodyData[0]) && $bodyData[0] instanceof File) {
                // ファイル配列の場合
                $contentType       = 'multipart/form-data';
                $bodyDataLength    = count($bodyData);
                for ($i = 0; $i < $bodyDataLength; ++$i) {
                    $properties[$name."[${i}]"] = ['type'   => 'string', 'format' => 'binary', 'description' => $this->request->formRequest ? $this->request->formRequest->getRuleDescription($name) : $name];
                }
            } elseif (is_object($bodyData) || is_array($bodyData)) {
                // ファイル配列でない構造体の場合
                return ['content'=> ['application/json' => [
                    'schema' => $this->makeSchema($this->request->data)
                ]]];
            } else {
                // 文字列等構造体でない場合
                $properties[$name] = array_merge(
                    ['description' => $this->request->formRequest ? $this->request->formRequest->getRuleDescription($name) : $name],
                    $this->makeSchema($bodyData)
                );
            }
        }
        if (empty($properties)) {
            return [];
        }

        return ['content' => [$contentType => [
            'schema' => ['type' => 'object', 'properties' => $properties]
        ]]];
    }

    /**
     * @param  array|string|number $content
     * @return array
     */
    private function makeSchema($content): array
    {
        if (! is_array($content)) {
            return static::convertToProperty($content);
        }
        $properties = collect($content)->map(fn ($v, $k) => array_merge(
            ['description' => $this->request->formRequest ? $this->request->formRequest->getRuleDescription($k) : $k],
            static::convertToProperty($v)
        ));

        if ($properties->isEmpty()) {
            return [];
        }

        return [
            'type'       => 'object',
            'properties' => $properties->toArray(),
        ];
    }

    private function getRequestPath(array $yamlRequest): array
    {
        [$url, $summary] = Url::getUrl($this->request->uri);

        $nameMatches     = [];
        preg_match_all('#{(\w+)}#', $url, $nameMatches);
        $valueMatches = [];
        preg_match_all('#/(\d+|\w{16,})(?:/|$)#', $this->request->uri, $valueMatches);
        if (count($valueMatches) <= 1) {
            return $yamlRequest;
        }
        foreach ($valueMatches[1] as $index => $value) {
            if ($value === []) {
                continue;
            }
            $swaggerFormat                = [];
            $swaggerFormat['name']        = $nameMatches[1][$index];
            $swaggerFormat['in']          = 'path';
            $swaggerFormat['required']    = true;
            $swaggerFormat['description'] = $value;
            $yamlRequest[]                = $swaggerFormat;
        }

        return $yamlRequest;
    }

    /** GETパラメータのrequestBody相当の部分をパースして構築 */
    protected function getRequestQuery(): array
    {
        $query                = [];
        $query['name']        = 'query';
        $query['in']          = 'query';
        $query['description'] = 'GETリクエストのパラメータ';
        $query['schema']      = $this->makeSchema($this->request->data);

        return $query['schema'] === [] ? [] : $query;
    }

    /**
     * Swagger上における型の名前を返す
     * @param  mixed  $value
     * @return string
     */
    protected function getTypeStrOnSwagger($value): string
    {
        if (is_array($value)) {
            return 'array';
        }

        if (is_bool($value)) {
            return 'boolean';
        }

        if (is_int($value)) {
            return  'integer';
        }

        if (is_numeric($value)) {
            return 'number';
        }

        if (is_string($value)) {
            return 'string';
        }

        throw new SwaggerYamlException('typeが未定義です. type:'.gettype($value));
    }

    /**
     * @return array
     */
    protected function getTags(): array
    {
        $tags =[];
        foreach (Tag::getTags($this->request->uri) as $index => $tag) {
            $tags[] = $tag->name;
        }

        return $tags;
    }
}
