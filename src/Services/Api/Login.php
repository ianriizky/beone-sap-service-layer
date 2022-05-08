<?php

namespace Ianriizky\BeoneSAPServiceLayer\Services\Api;

use Ianriizky\BeoneSAPServiceLayer\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;

/**
 * Log in Service Layer with the specified credentials. Generally it is the first step to
 * using the Service Layer API. Calling the Service Layer API without a login will result
 * in failure. After logging in successfully, one session ID will be returned in HTTP
 * response body. At the same time, two HTTP cookie items named "B1SESSION" and
 * "ROUTEID" will be set. You do not need to relate to them if calling the
 * Service Layer API in a browser because the browser will send them to
 * Service Layer automatically in subsequent HTTP requests. You do
 * need to add them to your HTTP header in a subsequent Service
 * Layer API call. Otherwise, Service Layer will consider them
 * as a bad request without a login.
 *
 * @see https://sap-samples.github.io/smb-summit-hackathon/b1sl.html
 *
 * @property \Ianriizky\BeoneSAPServiceLayer\Http\Client\PendingRequest $request
 */
trait Login
{
    /**
     * Create "/Login" POST request to the SAP service layer.
     *
     * Login Service Layer with the specified credentials.
     *
     * @param  array  $data
     * @return \Illuminate\Http\Client\Response
     */
    protected function postLogin(array $data): Response
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
