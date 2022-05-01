<?php

namespace Ianriizky\BeoneSAPServiceLayer\Services;

use BadMethodCallException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Traits\Macroable;
use RuntimeException;

/**
 * @see https://sap-samples.github.io/smb-summit-hackathon/b1sl.html
 */
class SAPServiceLayer
{
    use Macroable {
        __call as macroCall;
    }
    use Api\ChartOfAccounts;
    use Concerns\HandleAuthentication;

    /**
     * List of escaped method when __call() is called.
     *
     * @var array<int, string>
     */
    public static $escapedMethods = [
        'createRequestInstance',
        'sendRequestToSAP',

        // Concerns\HandleAuthentication
        'isRequestAuthenticated',
        'authenticateRequest',
        'getCookiesFromLogin',
        'getCredentials',
        'retryRequestWhenUnauthorized',

        // Api\Login
        'sendLoginRequest',
    ];

    /**
     * Instance of \Illuminate\Http\Client\PendingRequest to build the request.
     *
     * @var \Illuminate\Http\Client\PendingRequest
     */
    protected $request;

    /**
     * List of config value.
     *
     * @var array
     */
    protected $config;

    /**
     * Create a new instance class.
     *
     * @param  array  $config
     * @param  string|bool|null  $sslVerify
     * @return void
     */
    public function __construct(array $config, $sslVerify = null)
    {
        $this->config = $config;

        $this->request = $this->createRequestInstance(
            $this->config['base_url'],
            $sslVerify,
            Arr::except($config['guzzle_options'], 'verify')
        );

        $this->request->withMiddleware($this->authenticateRequest());

        $this->request->retry(
            $config['request_retry_times'],
            $config['request_retry_sleep'],
            $this->retryRequestWhenUnauthorized(),
            true
        );
    }

    /**
     * Send a request to the SAP service layer based on the given method and parameters.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return \Illuminate\Http\Client\Response
     *
     * @throws \RuntimeException
     */
    protected function sendRequestToSAP(string $method, array $parameters = []): Response
    {
        $response = $this->{$method}(...$parameters);

        if (! $response instanceof Response) {
            throw new RuntimeException(sprintf(
                'The return value from method %s::%s must be an instance of %s class.',
                static::class, $method, Response::class
            ));
        }

        // Use throw() method to make sure that it's always throw an exception
        // when the given response is error.
        return $response->throw();
    }

    /**
     * Dynamically handle calls to the class.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        if (in_array($method, static::$escapedMethods, true)) {
            throw new BadMethodCallException(sprintf(
                'Method %s::%s is in the escaped method list.', static::class, $method
            ));
        }

        return $this->sendRequestToSAP($method, $parameters);
    }
}
