<?php

namespace Ianriizky\BeoneSAPServiceLayer\Services;

use BadMethodCallException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
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
    use Api\Login;
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
        'reattemptLoginWhenUnauthorized',

        // Api\Login
        'sendLoginRequest',
    ];

    /**
     * Instance of \Illuminate\Http\Client\PendingRequest to make the request.
     *
     * @var \Illuminate\Http\Client\PendingRequest
     */
    protected PendingRequest $request;

    /**
     * Create a new instance class.
     *
     * @param  array  $config
     * @param  string|bool|null  $sslVerify
     * @return void
     */
    public function __construct(protected array $config, $sslVerify = null)
    {
        $this->createRequestInstance(
            $sslVerify, Arr::except($config['guzzle_options'], 'verify')
        );

        $this->reattemptLoginWhenUnauthorized(
            $config['request_retry_times'],
            $config['request_retry_sleep']
        );
    }

    /**
     * Create Laravel HTTP client request instance.
     *
     * @param  string|bool|null  $sslVerify
     * @param  array  $options
     * @return void
     */
    protected function createRequestInstance($sslVerify = null, array $options)
    {
        $this->request = Http::baseUrl($this->config['base_url'])->withOptions($options);

        if (! is_null($sslVerify)) {
            $this->request->withOptions(['verify' => $sslVerify]);
        }
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

        if (! $this->isRequestAuthenticated()) {
            $this->authenticateRequest();
        }

        return $this->sendRequestToSAP($method, $parameters);
    }
}
