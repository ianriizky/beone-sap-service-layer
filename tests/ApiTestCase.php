<?php

namespace Ianriizky\BeoneSAPServiceLayer\Tests;

use Ianriizky\BeoneSAPServiceLayer\Support\Facades\Http;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Testing\Assert;
use Mockery as m;

class ApiTestCase extends TestCase
{
    /**
     * @var \Ianriizky\BeoneSAPServiceLayer\Http\Client\Factory
     */
    protected $factory;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = Http::getFacadeRoot();

        $this->factory->macro('responseFromJsonPath', function (string $jsonPath, $status = 200, $headers = []) {
            /** @var \Ianriizky\TalentaApi\Http\Client\Factory $factory */
            $factory = $this;
            $body = json_decode(ApiTestCase::getJsonFromResponsesPath($jsonPath), true);

            return $factory->response($body, $status, $headers);
        });

        $this->factory->macro('fakeUsingJsonPath', function (string $jsonPath, $status = 200, $headers = []) {
            /** @var \Ianriizky\TalentaApi\Http\Client\Factory $factory */
            $factory = $this;

            $factory->fake(function (Request $request) use ($factory, $jsonPath, $status, $headers) {
                // todo: assert request cookies authentication here

                return $factory->responseFromJsonPath($jsonPath, $status, $headers);
            });
        });

        Response::macro('assertSameWithJsonPath', function (string $expectedJsonPath) {
            /** @var \Illuminate\Http\Client\Response $actualResponse */
            $actualResponse = $this;

            Assert::assertJsonStringEqualsJsonString(
                ApiTestCase::getJsonFromResponsesPath($expectedJsonPath),
                $actualResponse->body()
            );
        });
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown(): void
    {
        m::close();
    }

    /**
     * Return JSON request from the given path.
     *
     * @param  string  $jsonPath
     * @return string|false
     */
    public static function getJsonFromRequestsPath(string $jsonPath)
    {
        return file_get_contents(__DIR__.'/requests/'.$jsonPath);
    }

    /**
     * Return JSON response from the given path.
     *
     * @param  string  $jsonPath
     * @return string|false
     */
    public static function getJsonFromResponsesPath(string $jsonPath)
    {
        return file_get_contents(__DIR__.'/responses/'.$jsonPath);
    }
}
