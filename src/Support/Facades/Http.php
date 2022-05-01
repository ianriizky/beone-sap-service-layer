<?php

namespace Ianriizky\BeoneSAPServiceLayer\Support\Facades;

use Ianriizky\BeoneSAPServiceLayer\Http\Client\Factory;
use Illuminate\Support\Facades\Http as LaravelHttp;

class Http extends LaravelHttp
{
    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return Factory::class;
    }
}
