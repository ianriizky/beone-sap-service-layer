<?php

namespace Ianriizky\BeoneSAPServiceLayer\Tests\Api;

use Ianriizky\BeoneSAPServiceLayer\Support\Facades\SAPServiceLayer;
use Ianriizky\BeoneSAPServiceLayer\Tests\ApiTestCase;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Response as HttpResponse;

/**
 * @see \Ianriizky\BeoneSAPServiceLayer\Services\Api\Login
 */
class LoginTest extends ApiTestCase
{
    public function test_postLogin_response_200()
    {
        $this->factory->fakeLogin();

        tap(SAPServiceLayer::postLogin([
            'CompanyDB' => env('SAP_COMPANY_DB'),
            'UserName' => env('SAP_USERNAME'),
            'Password' => env('SAP_PASSWORD'),
        ]), function ($response) {
            /** @var \Illuminate\Http\Client\Response $response */
            $this->assertInstanceOf(Response::class, $response);

            $this->assertObjectHasAttribute('odata.metadata', $response->object());
            $this->assertObjectHasAttribute('SessionId', $response->object());
            $this->assertObjectHasAttribute('Version', $response->object());
            $this->assertObjectHasAttribute('SessionTimeout', $response->object());
        });

        $this->factory->assertSentCount(1);
    }

    public function test_postLogin_response_401_invalid_companydb()
    {
        $this->factory->fakeLogin();

        $this->expectException(RequestException::class);
        $this->expectExceptionCode(HttpResponse::HTTP_UNAUTHORIZED);

        try {
            SAPServiceLayer::postLogin([
                'CompanyDB' => $companyDB = env('SAP_COMPANY_DB').'s',
                'UserName' => env('SAP_USERNAME'),
                'Password' => env('SAP_PASSWORD'),
            ]);
        } catch (RequestException $th) {
            $this->assertEquals(
                'company not exist in company object cache : server(hanab1dev.beonesolution.com:30015) company('.$companyDB,
                $th->response->json('error.message.value')
            );

            throw $th;
        }

        $this->factory->assertSentCount(1);
    }

    public function test_postLogin_response_401_invalid_credentials()
    {
        $this->factory->fakeLogin();

        $this->expectException(RequestException::class);
        $this->expectExceptionCode(HttpResponse::HTTP_UNAUTHORIZED);

        try {
            SAPServiceLayer::postLogin([
                'CompanyDB' => env('SAP_COMPANY_DB'),
                'UserName' => env('SAP_USERNAME').'s',
                'Password' => env('SAP_PASSWORD').'s',
            ]);
        } catch (RequestException $th) {
            $this->assertEquals(
                'Login failed',
                $th->response->json('error.message.value')
            );

            throw $th;
        }

        $this->factory->assertSentCount(1);
    }
}
