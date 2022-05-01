<?php

namespace Ianriizky\BeoneSAPServiceLayer\Http\Client;

use Illuminate\Http\Client\PendingRequest as LaravelPendingRequest;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Collection;
use Psr\Http\Message\RequestInterface;

class PendingRequest extends LaravelPendingRequest
{
    /**
     * {@inheritDoc}
     */
    public function __construct(Factory $factory = null)
    {
        $this->factory = $factory;
        $this->middleware = new Collection;

        $this->asJson();

        $this->options = [
            'connect_timeout' => 10,
            'http_errors' => false,
            'timeout' => 30,
        ];

        $this->beforeSendingCallbacks = collect([$this->dispatchRequestSendingEventCallback()]);
    }

    /**
     * Before sending callback to dispatch the request sending event.
     *
     * @return callable
     */
    protected function dispatchRequestSendingEventCallback()
    {
        return function (Request $request, array $options, PendingRequest $pendingRequest): RequestInterface {
            $pendingRequest->request = $request;
            $pendingRequest->cookies = $options['cookies'];

            $pendingRequest->dispatchRequestSendingEvent();

            return $request->toPsrRequest();
        };
    }

    /**
     * Add new middleware the client handler stack with its name.
     *
     * @param  callable  $middleware
     * @param  string  $name
     * @return $this
     */
    public function withMiddlewareAndName(callable $middleware, string $name)
    {
        $this->middleware->put($middleware, $name);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function pushHandlers($handlerStack)
    {
        return tap($handlerStack, function ($stack) {
            $stack->push($this->buildBeforeSendingHandler());
            $stack->push($this->buildRecorderHandler());

            $this->middleware->each(function ($middleware, $name) use ($stack) {
                $stack->push($middleware, $name);
            });

            $stack->push($this->buildStubHandler());
        });
    }

    /**
     * {@inheritDoc}
     *
     * @return \Psr\Http\Message\RequestInterface
     */
    public function runBeforeSendingCallbacks($request, array $options)
    {
        return tap($request, function (&$request) use ($options) {
            $this->beforeSendingCallbacks->each(function ($callback) use (&$request, $options) {
                $request = call_user_func(
                    $callback, (new Request($request))->withData($options['laravel_data']), $options, $this
                );
            });
        });
    }
}
