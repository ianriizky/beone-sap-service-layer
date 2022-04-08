<?php

namespace Ianriizky\BeoneSAPServiceLayer\Services\Concerns;

use Closure;
use GuzzleHttp\Cookie\CookieJar;
use Ianriizky\BeoneSAPServiceLayer\Services\Api;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

/**
 * @property \Illuminate\Http\Client\PendingRequest $request
 * @property array $config
 */
trait HandleAuthentication
{
    use Api\Login;

    /**
     * Determine whether the request instance is authenticated or not.
     *
     * @param  \Illuminate\Http\Client\PendingRequest  $request
     * @return bool
     */
    protected static function isRequestAuthenticated(PendingRequest $request): bool
    {
        return Arr::exists($request->getOptions(), 'cookies');
    }

    /**
     * Create a callback to set authentication data before sending the request.
     *
     * @return \Closure
     */
    protected function authenticateRequest(): Closure
    {
        return function (Request $request, array $options, PendingRequest $pendingRequest) {
            if (! static::isRequestAuthenticated($pendingRequest) && !Str::contains($request->url(), '/Login')) {
                $pendingRequest->withOptions(['cookies' => $this->getCookiesFromLogin($pendingRequest)]);
            }
        };
    }

    /**
     * Return cookies value from the "/Login" request for authentication purpose.
     *
     * @param  \Illuminate\Http\Client\PendingRequest  $request
     * @return \GuzzleHttp\Cookie\CookieJar
     */
    protected function getCookiesFromLogin(PendingRequest $request): CookieJar
    {
        return static::sendLoginRequest($this->getCredentials(), $request)->cookies();
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
     * Register closure on the request instance to handle request retrying process
     * when the given response is unauthorized.
     *
     * @param  int  $times
     * @param  int  $sleep
     * @param  bool  $throw
     * @return void
     */
    protected function retryRequestWhenUnauthorized(int $times, int $sleep = 0, bool $throw = true)
    {
        $this->request->retry($times, $sleep, function (Throwable $exception, PendingRequest $request) {
            if (! $exception instanceof RequestException || $exception->getCode() !== HttpResponse::HTTP_UNAUTHORIZED) {
                return false;
            }

            $newRequestInstanceWithoutRetrying =
                Http::baseUrl($this->config['base_url'])
                    ->withOptions($request->getOptions());

            $request->withOptions(['cookies' => $this->getCookiesFromLogin($newRequestInstanceWithoutRetrying)]);

            return true;
        }, $throw);
    }
}
