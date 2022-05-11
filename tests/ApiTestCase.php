<?php

namespace Ianriizky\BeoneSAPServiceLayer\Tests;

use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Testing\Assert;
use Mockery as m;

class ApiTestCase extends TestCase
{
    /**
     * @var \Illuminate\Http\Client\Factory
     */
    protected $factory;

    /**
     * @var string
     */
    public $sessionId;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = Http::getFacadeRoot();

        $testCaseClass = $this;

        $this->factory->macro('failedLoginResponse', function (array $credentials) {
            /** @var \Illuminate\Http\Client\Factory $factory */
            $factory = $this;

            return $factory->response([
                'error' => [
                    'code' => 100000027,
                    'message' => [
                        'lang' => 'en-us',
                        'value' => $credentials['CompanyDB'] !== env('SAP_COMPANY_DB')
                            ? 'company not exist in company object cache : server(hanab1dev.beonesolution.com:30015) company('.$credentials['CompanyDB']
                            : 'Login failed',
                    ],
                ],
            ], HttpResponse::HTTP_UNAUTHORIZED, [
                'Date' => [Carbon::now()->toRfc7231String()],
                'Server' => ['Apache/2.4.34 (Unix)'],
                'DataServiceVersion' => ['3.0'],
                'Content-Type' => ['application/json;charset=utf-8'],
                'Vary' => ['Accept-Encoding'],
                'Set-Cookie' => [
                    'ROUTEID=.node1; path=/b1s',
                ],
                'Transfer-Encoding' => ['chunked'],
            ]);
        });

        $this->factory->macro('successLoginResponse', function (Request $request) use (&$testCaseClass) {
            /** @var \Illuminate\Http\Client\Factory $factory */
            $factory = $this;

            return $factory->response([
                'odata.metadata' => Str::replace('/Login', '/$metadata#B1Sessions/@Element', $request->url()),
                'SessionId' => $testCaseClass->sessionId = Str::uuid(),
                'Version' => '930230',
                'SessionTimeout' => 30,
            ], HttpResponse::HTTP_OK, [
                'Date' => [Carbon::now()->toRfc7231String()],
                'Server' => ['Apache/2.4.34 (Unix)'],
                'DataServiceVersion' => ['3.0'],
                'Content-Type' => ['application/json;odata=minimalmetadata;charset=utf-8'],
                'Set-Cookie' => [
                    'B1SESSION='.$testCaseClass->sessionId.';HttpOnly;',
                    'CompanyDB='.env('SAP_COMPANY_DB').';HttpOnly;',
                    'ROUTEID=.node1; path=/b1s',
                ],
                'Vary' => ['Accept-Encoding'],
                'Transfer-Encoding' => ['chunked'],
            ]);
        });

        $this->factory->macro('responseFromJsonPath', function (string $jsonPath, $status = HttpResponse::HTTP_OK, $headers = []) {
            /** @var \Illuminate\Http\Client\Factory $factory */
            $factory = $this;
            $body = json_decode(ApiTestCase::getJsonFromResponsesPath($jsonPath), true);

            return $factory->response($body, $status, $headers);
        });

        $this->factory->macro('fakeLogin', function () {
            /** @var \Illuminate\Http\Client\Factory $factory */
            $factory = $this;

            $factory->fake(ApiTestCase::validateLoginCallback($factory));
        });

        $this->factory->macro('fakeUsingJsonPath', function (string $jsonPath, $status = HttpResponse::HTTP_OK, $headers = []) {
            /** @var \Illuminate\Http\Client\Factory $factory */
            $factory = $this;

            $factory->fake(function (Request $request) use ($factory, $jsonPath, $status, $headers) {
                if (Str::contains($request->url(), '/Login')) {
                    return ApiTestCase::validateLoginCallback($factory)($request);
                }

                Assert::assertTrue($request->hasSAPAuthenticationHeader());

                return $factory->responseFromJsonPath($jsonPath, $status, $headers);
            });
        });

        Request::macro('hasSAPAuthenticationHeader', function () use ($testCaseClass) {
            /** @var \Illuminate\Http\Client\Request $request */
            $request = $this;

            return $request->hasHeader(
                'Cookie',
                'B1SESSION='.$testCaseClass->sessionId.'; CompanyDB='.env('SAP_COMPANY_DB').'; ROUTEID=.node1'
            );
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

    /**
     * Create callback to run login validation process.
     *
     * @param  \Illuminate\Http\Client\Factory  $factory
     * @return callable
     */
    public static function validateLoginCallback(Factory $factory)
    {
        return function (Request $request) use ($factory) {
            $credentials = json_decode($request->body(), true);

            if ($credentials['CompanyDB'] === env('SAP_COMPANY_DB') &&
                $credentials['UserName'] === env('SAP_USERNAME') &&
                $credentials['Password'] === env('SAP_PASSWORD')) {
                return $factory->successLoginResponse($request);
            }

            return $factory->failedLoginResponse($credentials);
        };
    }
}
