<?php

namespace Tests;

use App\Http\HttpStatus;
use Arr;
use Illuminate\Foundation\Testing\TestResponse;

/**
 * Trait ApiTestTrait
 * @package Tests
 * @mixin TestCase
 */
trait ApiTestTrait
{
    /** @var TestResponse */
    protected $response;

    /**
     * レスポンスのボディをdump関数で表示
     */
    public function dumpApiResponse(): void
    {
        $responseData = json_decode($this->response->getContent(), true);
        /* @noinspection ForgottenDebugOutputInspection */
        dump($responseData);
    }

    /**
     * レスポンスのボディをdump関数で表示
     */
    public function dumpApiResponseBody(): void
    {
        $responseData = $this->getDecodedResponseBody();
        /* @noinspection ForgottenDebugOutputInspection */
        dump($responseData);
    }

    /**
     * Assert that the response has the given status code.
     *
     * @param int $status
     */
    public function assertStatus(int $status): void
    {
        $actual = $this->response->getStatusCode();

        $actualStr = HttpStatus::$statusTexts[$actual];
        $statusStr = HttpStatus::$statusTexts[$status];
        self::assertSame(
            $actual,
            $status,
            "Expected status code {$status} but received {$actual}.\n{$status} is \"{$statusStr}\". {$actual} is \"{$actualStr}\"."
        );
    }

    public function assertApiResponseAll(array $actualData): void
    {
        $responseData = $this->getDecodedResponseBody();

        $this->assertModelData($actualData, $responseData);
    }

    public function assertApiMessageEquals($expectedValue): void
    {
        $responseData = $this->getDecodedResponse();
        self::assertEquals($expectedValue, $responseData['message']);
    }

    /**
     * ネストの深いキーはドット記法で探索.
     *
     * ex. [
     *    'hoge' => [
     *        'fuga' => 1,
     *     ]
     * ];
     * assertApiResponseEquals('hoge.fuga', 1) === true
     * @param $key
     * @param $expectedValue
     */
    public function assertApiResponseEquals($key, $expectedValue): void
    {
        $responseData = $this->getDecodedResponseBody();
        self::assertEquals($expectedValue, Arr::get($responseData, $key));
    }

    /**
     * ネストの深いキーはドット記法で探索.
     *
     * ex. [
     *    'hoge' => [
     *        'fuga' => 'foobar',
     *     ]
     * ];
     * assertApiResponsePregMatch('hoge.fuga', '/^foo/') === true
     * @param $key
     * @param $expectedValue
     */
    public function assertApiResponsePregMatch($key, $expectedValue): void
    {
        $responseData = $this->getDecodedResponseBody();
        self::assertNotFalse(preg_match($expectedValue, Arr::get($responseData, $key)));
    }

    /**
     * ネストの深いキーはドット記法で探索.
     *
     * ex. [
     *    'hoge' => [
     *        'fuga' => 1,
     *     ]
     * ];
     * assertApiResponseEquals('hoge.fuga', 1) === true
     * @param $key
     * @param $expectedValue
     */
    public function assertApiResponseNotEquals($key, $expectedValue): void
    {
        $responseData = $this->getDecodedResponseBody();
        self::assertNotEquals($expectedValue, Arr::get($responseData, $key));
    }

    public function assertApiResponseHas($key): void
    {
        $responseData = $this->getDecodedResponseBody();
        self::assertTrue(Arr::has($responseData, $key), "レスポンスに${key}が見つかりません");
    }

    public function assertApiResponseHasNot($key): void
    {
        $responseData = $this->getDecodedResponseBody();
        self::assertNotTrue(array_key_exists($key, $responseData));
    }

    public function assertApiSuccess(): void
    {
        $this->dumpApiResponse();
        $this->assertStatus(HttpStatus::OK);
        // {message:string, body:any, success:boolean}の形式時に使用
        $this->response->assertJson(['success' => true]);
    }

    public function assertApiFailed($code): void
    {
        $this->dumpApiResponse();
        $this->assertStatus($code);
    }

    public function assertApiSuccessIsFalse(): void
    {
        $this->response->assertJson(['success' => false]);
    }

    public function assertApiOK(): void
    {
        $this->assertStatus(HttpStatus::OK);
    }


    public function assertApiNotFound(): void
    {
        $this->assertStatus(HttpStatus::NOT_FOUND);
    }

    public function assertModelData(array $actualData, array $expectedData): void
    {
        foreach ($actualData as $key => $value) {
            if (in_array($key, ['created_at', 'updated_at'])) {
                continue;
            }
            self::assertEquals($actualData[$key], $expectedData[$key]);
        }
    }

    /**
     * @param array|string|number $expectErrorKeys
     */
    public function assertApiValidationFailed($expectErrorKeys): void
    {
        if (! is_array($expectErrorKeys)) {
            $expectErrorKeys = [$expectErrorKeys];
        }
        $this->assertStatus(HttpStatus::UNPROCESSABLE_ENTITY);
        $errors = $this->getDecodedResponseBody()['errors'];
        foreach ($expectErrorKeys as $errorKey) {
            self::assertArrayHasKey($errorKey, $errors);
        }
    }

    /**
     * レスポンスのデータ本体を抜き出す
     * @return array
     */
    public function getDecodedResponseBody(): array
    {
        return json_decode($this->response->getContent(), true)['body'];
    }

    /**
     * レスポンス全体をデコード
     * @return array
     */
    public function getDecodedResponse(): array
    {
        return json_decode($this->response->getContent(), true);
    }
}
