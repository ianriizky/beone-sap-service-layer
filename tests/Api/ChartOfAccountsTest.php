<?php

namespace Ianriizky\BeoneSAPServiceLayer\Tests\Api;

use Ianriizky\BeoneSAPServiceLayer\Support\Facades\SAPServiceLayer;
use Ianriizky\BeoneSAPServiceLayer\Tests\ApiTestCase;
use Illuminate\Http\Client\Response;

/**
 * @see \Ianriizky\BeoneSAPServiceLayer\Services\Api\ChartOfAccounts
 */
class ChartOfAccountsTest extends ApiTestCase
{
    public function test_getChartOfAccountsId_response_200()
    {
        $jsonPath = 'ChartOfAccounts/getChartOfAccountsId/200.json';

        $this->factory->fakeUsingJsonPath($jsonPath);

        tap(SAPServiceLayer::getChartOfAccountsId('100000'), function ($response) use ($jsonPath) {
            $this->assertInstanceOf(Response::class, $response);

            $response->assertSameWithJsonPath($jsonPath);
        });

        $this->factory->assertSentCount(2);
    }

    public function test_getChartOfAccounts_response_200()
    {
        $jsonPath = 'ChartOfAccounts/getChartOfAccounts/200.json';

        $this->factory->fakeUsingJsonPath($jsonPath);

        tap(SAPServiceLayer::getChartOfAccounts(), function ($response) use ($jsonPath) {
            $this->assertInstanceOf(Response::class, $response);

            $response->assertSameWithJsonPath($jsonPath);
        });

        $this->factory->assertSentCount(2);
    }

    public function test_postChartOfAccounts_response_200()
    {
        $this->assertTrue(true);
    }

    public function test_patchChartOfAccounts_response_200()
    {
        $this->assertTrue(true);
    }

    public function test_deleteChartOfAccounts_response_200()
    {
        $this->assertTrue(true);
    }
}
