<?php

namespace Ianriizky\BeoneSAPServiceLayer\Http\Client;

use Illuminate\Http\Client\Factory as LaravelFactory;

class Factory extends LaravelFactory
{
    /**
     * {@inheritDoc}
     *
     * @return \Ianriizky\BeoneSAPServiceLayer\Http\Client\PendingRequest
     */
    protected function newPendingRequest()
    {
        return new PendingRequest($this);
    }
}
