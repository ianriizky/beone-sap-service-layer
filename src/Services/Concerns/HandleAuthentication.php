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
use Illuminate\Support\Str;
use Throwable;

/**
 * @property \Illuminate\Http\Client\PendingRequest $request
 * @property array $config
 */
trait HandleAuthentication
{
    use Api\Login;
    use HandlePendingRequest;

    /**
     * Create a callback to set authentication data before sending the request.
     *
     * @return \Closure
     */
    protected function authenticateRequest(): Closure
    {
        return function (Request $request, array $options, PendingRequest $pendingRequest) {
            if (! Str::contains($request->url(), '/Login') && ! static::isRequestAuthenticated($pendingRequest)) {
                $pendingRequest->withOptions([
                    'cookies' => $this->getCookiesFromLogin($pendingRequest),
                ]);
            }
        };
    }

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
     * @return array<string, string>
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
     * Create a callback to handle request retrying process when the given response is unauthorized.
     *
     * @return \Closure
     */
    protected function retryRequestWhenUnauthorized(): Closure
    {
        return function (Throwable $exception, PendingRequest $request) {
            if (! $exception instanceof RequestException || $exception->getCode() !== HttpResponse::HTTP_UNAUTHORIZED) {
                return false;
            }

            $request->withOptions([
                'cookies' => $this->getCookiesFromLogin($this->createFreshRequestInstance()),
            ]);

            return true;
        };
    }
}
