<?php

namespace Ianriizky\BeoneSAPServiceLayer\Services\Concerns;

use Closure;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\CookieJarInterface;
use GuzzleHttp\Cookie\SetCookie;
use Ianriizky\BeoneSAPServiceLayer\Services\Api;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
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
     * @return callable
     */
    protected function authenticateRequest(): callable
    {
        return function (callable $handler): callable {
            return function (RequestInterface $request, array $options) use ($handler) {
                return $handler((function (RequestInterface $request) use ($options) {
                    if (Str::contains((string) $request->getUri(), '/Login')) {
                        return $request;
                    }

                    $cookies = $options['cookies'] ?? null;

                    if (! $cookies instanceof CookieJarInterface) {
                        throw new InvalidArgumentException('Cookies must be an instance of ' . CookieJarInterface::class);
                    }

                    if (! static::isRequestAuthenticated($cookies)) {
                        return $this->getCookiesFromLogin($this->request)->withCookieHeader($request);
                    }

                    return $request;
                })($request), $options);
            };
        };
    }

    /**
     * Determine whether the request instance is authenticated or not.
     *
     * @param  \GuzzleHttp\Cookie\CookieJarInterface  $cookies
     * @return bool
     */
    protected static function isRequestAuthenticated(CookieJarInterface $cookies): bool
    {
        return collect($cookies)->filter(function (SetCookie $cookie) {
            return $cookie->getName() === 'B1SESSION';
        })->count() > 0;
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
