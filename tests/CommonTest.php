<?php

namespace Ianriizky\BeoneSAPServiceLayer\Tests;

use Ianriizky\BeoneSAPServiceLayer\Support\Facades\SAPServiceLayer;
use Illuminate\Http\Client\PendingRequest;

class CommonTest extends TestCase
{
    public function test_that_sap_service_layer_request_method_has_correct_return_value()
    {
        $this->assertInstanceOf(PendingRequest::class, SAPServiceLayer::request());
    }
}
