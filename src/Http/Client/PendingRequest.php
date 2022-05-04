<?php

namespace Ianriizky\BeoneSAPServiceLayer\Http\Client;

use Illuminate\Http\Client\PendingRequest as LaravelPendingRequest;
use Illuminate\Http\Client\Request;
use Psr\Http\Message\RequestInterface;

class PendingRequest extends LaravelPendingRequest
{
    /**
     * {@inheritDoc}
     *
     * @return \Psr\Http\Message\RequestInterface
     */
    public function runBeforeSendingCallbacks($request, array $options)
    {
        return tap($request, function (&$request) use ($options) {
            $this->beforeSendingCallbacks->each(function ($callback) use (&$request, $options) {
                $callbackResult = call_user_func(
                    $callback, (new Request($request))->withData($options['laravel_data']), $options, $this
                );

                if ($callbackResult instanceof RequestInterface) {
                    $request = $callbackResult;
                } elseif ($callbackResult instanceof Request) {
                    $request = $callbackResult->toPsrRequest();
                }
            });
        });
    }
}
