<?php

namespace Ianriizky\BeoneSAPServiceLayer\Services\Api;

use Ianriizky\BeoneSAPServiceLayer\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;

/**
 * @property \Ianriizky\BeoneSAPServiceLayer\Http\Client\PendingRequest $request
 */
trait Login
{
    /**
     * Create "/Login" POST request to the SAP service layer.
     *
     * @param  array  $data
     * @return \Illuminate\Http\Client\Response
     */
    protected function Login(array $data): Response
    {
        return static::sendLoginRequest($data, $this->request);
    }

    /**
     * Send login request to the SAP service layer.
     *
     * This will method is run by using an independent $request instance from the parameter,
     * so the $request property from the class will not be affected by this method.
     *
     * @param  array  $data
     * @param  \Ianriizky\BeoneSAPServiceLayer\Http\Client\PendingRequest  $request
     * @return \Illuminate\Http\Client\Response
     */
    protected static function sendLoginRequest(array $data, PendingRequest $request): Response
    {
        return $request->post('/Login', Arr::only($data, [
            'CompanyDB', 'UserName', 'Password',
        ]));
    }
}
