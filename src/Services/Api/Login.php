<?php

namespace Ianriizky\BeoneSAPServiceLayer\Services\Api;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;

/**
 * @property \Illuminate\Http\Client\PendingRequest $request
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
        return $this->sendLoginRequest($data, $this->request);
    }

    /**
     * Send login request to the SAP service layer.
     * This will method is run by using an independent $request instance from the parameter,
     * so the $request property from the class will not be affected by this process.
     *
     * @param  array  $data
     * @param  \Illuminate\Http\Client\PendingRequest|null  $request
     * @return \Illuminate\Http\Client\Response
     */
    protected function sendLoginRequest(array $data, PendingRequest $request = null): Response
    {
        return ($request ?? $this->request)->post('/Login', Arr::only($data, [
            'CompanyDB', 'UserName', 'Password',
        ]));
    }
}
