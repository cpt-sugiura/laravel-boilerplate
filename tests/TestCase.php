<?php

namespace Tests;

use App\Http\Requests\BaseFormRequest;
use App\Models\Eloquents\BaseEloquent;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Collection;
use Illuminate\Testing\TestResponse;
use ReflectionClass;
use ReflectionException;
use Storage;
use Str;
use Symfony\Component\Yaml\Yaml;
use Tests\ApiToSwaggerYamlPresenter\ApiToSwaggerYamlPresenter;
use Tests\ApiToSwaggerYamlPresenter\ConstantValues\Tag;
use Tests\ApiToSwaggerYamlPresenter\ConstantValues\Url;
use Tests\ApiToSwaggerYamlPresenter\SwaggerRequest;
use Throwable;

class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /** APIテスト時に用いる。主に結果をコンソールに通知する用 */
    protected array $request;
    private int $applicationUserCompanyId;

    protected function setUpTraits()
    {
        $uses =  parent::setUpTraits();
        if (isset($uses[WithoutAuthMiddleware::class])) {
            /* @noinspection PhpUndefinedMethodInspection */
            $this->disableAuthMiddlewareForAllTests();
        }
        if (isset($uses[WithoutCSRFMiddleware::class])) {
            /* @noinspection PhpUndefinedMethodInspection */
            $this->disableCSRFMiddlewareForAllTests();
        }

        return $uses;
    }

    /**
     * @param  string|BaseEloquent  $baseEloquent
     * @return BaseEloquent
     */
    protected function getRandomFirst(BaseEloquent|string $baseEloquent): BaseEloquent
    {
        return $baseEloquent::query()->inRandomOrder()->first();
    }

    /**
     * private, protectedメソッドをテストするためのアクセス権無視メソッド起動メソッド
     * @param  object              $instance   インスタンス化されたテスト対象クラス
     * @param  string              $methodName テスト対象メソッド名
     * @param  mixed               ...$args    テスト対象メソッドに渡す引数
     * @throws ReflectionException
     * @return mixed
     */
    public function reflectionInvoke(object $instance, string $methodName, ...$args): mixed
    {
        $reflection = new ReflectionClass($instance);
        $method     = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invoke($instance, ...$args);
    }

    /**
     * APIテスト時のリクエストログをインタンスに残しておく
     * @param  string  $method
     * @param  string  $uri
     * @param  array   $data
     * @param  array   $headers
     * @return TestResponse
     */
    public function json($method, $uri, array $data = [], array $headers = []): TestResponse
    {
        $this->request = compact('method', 'uri', 'data', 'headers');

        return parent::json($method, $uri, $data, $headers);
    }

    /**
     * {@inheritdoc}
     * @throws Throwable
     */
    protected function tearDown(): void
    {
        method_exists($this, 'dumpApiResponse') && $this->dumpApiResponse();
        parent::tearDown();
        if ($this->hasFailed()) {
            // コンソールへ通知するためのdump関数
            /* @noinspection ForgottenDebugOutputInspection */
            dump('failed in '.static::class.'::'.$this->getName().'. test case instace is');
            $test_class_data = (new Collection($this))->filter(
                static function ($key) {
                    return ! Str::contains($key, 'PHPUnit')
                        && ! Str::contains($key, 'faker')
                        && ! Str::contains($key, 'afterApplicationCreatedCallbacks')
                        && ! Str::contains($key, 'beforeApplicationDestroyedCallbacks')
                        && ! Str::contains($key, 'callbackException')
                        && ! Str::contains($key, 'setUpHasRun')
                        && ! Str::contains($key, 'backupGlobals')
                        && ! Str::contains($key, 'backupGlobalsBlacklist')
                        && ! Str::contains($key, 'backupStaticAttributes')
                        && ! Str::contains($key, 'backupStaticAttributesBlacklist')
                        && ! Str::contains($key, 'runTestInSeparateProcess')
                        && ! Str::contains($key, 'preserveGlobalState')
                        && ! Str::contains($key, 'originalMix')
                        && ! Str::contains($key, 'defaultHeaders')
                        && ! Str::contains($key, 'defaultCookies')
                        && ! Str::contains($key, 'unencryptedCookies')
                        && ! Str::contains($key, 'serverVariables')
                        && ! Str::contains($key, 'followRedirects')
                        && ! Str::contains($key, 'encryptCookies')
                        && ! Str::contains($key, 'mockConsoleOutput')
                        && ! Str::contains($key, 'expectedOutput')
                        && ! Str::contains($key, 'expectedQuestions')
                        && ! Str::contains($key, 'originalExceptionHandler')
                        && ! Str::contains($key, 'firedEvents')
                        && ! Str::contains($key, 'firedModelEvents')
                        && ! Str::contains($key, 'dispatchedJobs')
                        && ! Str::contains($key, 'dispatchedNotifications');
                }
            );
            /* @noinspection ForgottenDebugOutputInspection */
            dump($test_class_data);
        }
    }

    /**
     * APIテスト時のリクエストログをインタンスに残しておく
     * @param  string               $method
     * @param  string               $uri
     * @param  array                $data
     * @param  array                $headers
     * @param  BaseFormRequest|null $formRequest
     * @return TestResponse
     */
    public function jsonWithSwagger(string $method, string $uri, array $data = [], array $headers = [], BaseFormRequest $formRequest = null): TestResponse
    {
        $this->request = compact('method', 'uri', 'data', 'headers');

        $response     = parent::json($method, $uri, $data, $headers);
        $apiType      = Url::getApiType($uri);
        $baseYamlPath = 'docs/swagger/member/api.yaml';

        if (! file_exists($baseYamlPath) || filesize($baseYamlPath) === 0) {
            file_put_contents(base_path($baseYamlPath), $this->getSwaggerInitStr($apiType));
        }
        $baseYaml = Yaml::parseFile(base_path($baseYamlPath)) ?: [];
        if (! is_array($baseYaml)) {
            $baseYaml = [];
        }
        $request = new SwaggerRequest($method, $uri, $data, $headers, $formRequest);
        $newYaml = (new ApiToSwaggerYamlPresenter($request, $response, $baseYaml))->toArray();
        $newYaml['info']['version'] = trim(shell_exec('git describe --tags'));
        $tmpPath = 'tmp/api_'.Str::lower(Str::random()).'.yaml';
        try {
            file_put_contents(base_path('docs/'.$tmpPath), Yaml::dump($newYaml, 1e5, 2, Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE));
        } catch (Exception $e) {
            /* @noinspection ForgottenDebugOutputInspection */
            dump($newYaml, $e);
        }
        try {
            $yamlStr = Storage::disk('docs')->get($tmpPath);
            file_put_contents(base_path($baseYamlPath), $yamlStr);
        } catch (FileNotFoundException $e) {
            /* @noinspection ForgottenDebugOutputInspection */
            dump($e);
        }
        Storage::disk('docs')->delete($tmpPath);

        return $response;
    }

    /**
     * @param  string $apiType
     * @return string
     * @see Url::API_TYPE_USER
     */
    protected function getSwaggerInitStr(string $apiType): string
    {
        $initStr = <<<DOC
openapi: 3.0.3
info:
    description: アプリAPIのドキュメント
    version: 1.0.0
    title: アプリAPI
servers:
  - url: https://hoge.test.cpt.jp
    description: ステージング環境
  - url: http://default.docker-host.local
    description: ローカル開発環境
components:
  securitySchemes:
    XApiToken:
      type: apiKey
      in: header
      name: x-api-token
      description: APIを用いるために必要なトークン
    Bearer:
      type: http
      scheme: bearer
      description: 認証情報の必要なAPIを用いるためのトークン
DOC;

        $initStr .= "\n".Tag::getTagDefineStr($apiType);
        $initStr .= "\npaths:";

        return $initStr;
    }
}
