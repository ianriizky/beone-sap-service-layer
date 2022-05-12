<?php

namespace Ianriizky\BeoneSAPServiceLayer\Services;

use BadMethodCallException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Traits\Macroable;
use RuntimeException;

/**
 * @see \Ianriizky\BeoneSAPServiceLayer\Services\Api\ChartOfAccounts
 *
 * @method \Illuminate\Http\Client\Response getChartOfAccountsId(string $id, array|string|null $query = null) Create "/ChartOfAccounts(:id)" GET request to the SAP service layer.
 * @method \Illuminate\Http\Client\Response getChartOfAccounts(array|string|null $query = null) Create "/ChartOfAccounts" GET request to the SAP service layer.
 * @method \Illuminate\Http\Client\Response postChartOfAccounts(array $data = []) Create "/ChartOfAccounts" POST request to the SAP service layer.
 * @method \Illuminate\Http\Client\Response patchChartOfAccounts(string $id, array $data = []) Create "/ChartOfAccounts(:id)" PATCH request to the SAP service layer.
 * @method \Illuminate\Http\Client\Response deleteChartOfAccounts(string $id, array $data = []) Create "/ChartOfAccounts(:id)" DELETE request to the SAP service layer.
 *
 * @see \Ianriizky\BeoneSAPServiceLayer\Services\Api\JournalEntries
 *
 * @method \Illuminate\Http\Client\Response getJournalEntriesId(string $id, array|string|null $query = null) Create "/JournalEntries(:id)" GET request to the SAP service layer.
 * @method \Illuminate\Http\Client\Response getJournalEntries(array|string|null $query = null) Create "/JournalEntries" GET request to the SAP service layer.
 * @method \Illuminate\Http\Client\Response postJournalEntries(array $data = []) Create "/JournalEntries" POST request to the SAP service layer.
 * @method \Illuminate\Http\Client\Response patchJournalEntries(string $id, array $data = []) Create "/JournalEntries(:id)" PATCH request to the SAP service layer.
 * @method \Illuminate\Http\Client\Response postJournalEntriesCancel(string $id, array $data = []) Create "/JournalEntries(:id)/Cancel" POST request to the SAP service layer.
 *
 * @see \Ianriizky\BeoneSAPServiceLayer\Services\Api\Login
 *
 * @method \Illuminate\Http\Client\Response postLogin(array $data) Create "/Login" POST request to the SAP service layer.
 *
 * @see https://sap-samples.github.io/smb-summit-hackathon/b1sl.html
 */
class SAPServiceLayer
{
    use Macroable {
        __call as macroCall;
    }
    use Api\ChartOfAccounts;
    use Api\JournalEntries;
    use Concerns\HandleAuthentication;

    /**
     * List of escaped method when __call() is called.
     *
     * @var array<int, string>
     */
    protected static $escapedMethods = [
        'sendRequestToSAP',
        'authenticateRequest',
        'isRequestAuthenticated',
        'getCookiesFromLogin',
        'getCredentials',
        'retryRequestWhenUnauthorized',
        'sendLoginRequest',
        'createRequestInstance',
        'createFreshRequestInstance',
        'parseBaseUrl',
    ];

    /**
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

        $this->request->beforeSending($this->authenticateRequest());

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
     * @return \Illuminate\Http\Client\PendingRequest
     */
    public function request(): PendingRequest
    {
        return $this->request;
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
