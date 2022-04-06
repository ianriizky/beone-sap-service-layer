<?php

namespace Ianriizky\BeoneSAPServiceLayer\Services\Concerns;

use Closure;
use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * @property \Illuminate\Http\Client\PendingRequest $request
 * @property array $config
 */
trait HandleAuthentication
{
    /**
     * Determine whether the cookies value of request instance has been set.
     *
     * @return bool
     */
    protected function isRequestAuthenticated(): bool
    {
        return Arr::exists($this->request->getOptions(), 'cookies');
    }

    /**
     * Set cookies value of request instance for authentication purpose.
     *
     * @return void
     */
    protected function authenticateRequest()
    {
        $this->request->withOptions(['cookies' => $this->getCookiesFromLogin()]);
    }

    /**
     * Return cookies value from the "/Login" request for authentication purpose.
     *
     * @return \GuzzleHttp\Cookie\CookieJar
     */
    protected function getCookiesFromLogin(): CookieJar
    {
        $data = $this->getCredentials();

        return $this->sendRequestToSAP('Login', compact('data'))->cookies();
    }

    /**
     * Return list of credential value from the config.
     *
     * @return array
     */
    protected function getCredentials(): array
    {
        return [
            'CompanyDB' => $this->config['company_db'],
            'UserName' => $this->config['username'],
            'Password' => $this->config['password'],
        ];
    }

    /**
     * Register closure on the request instance to handle login re-attempt process
     * when the given response is unauthorized.
     *
     * @param  int  $times
     * @param  int  $sleep
     * @param  bool  $throw
     * @return void
     */
    protected function reattemptLoginWhenUnauthorized(int $times, int $sleep = 0, bool $throw = true)
    {
        $this->request->retry($times, $sleep, function (Throwable $exception, PendingRequest $request) {
            if (! $exception instanceof RequestException || $exception->getCode() !== HttpResponse::HTTP_UNAUTHORIZED) {
                return false;
            }

            $request = Http::baseUrl($this->config['base_url'])
                ->withOptions($this->request->getOptions());

            $newCookies = $this->sendLoginRequest([
                'data' => $this->getCredentials(),
            ], $request);

            $request->withOptions(['cookies' => $newCookies]);

            return true;
        }, $throw);
    }
}
