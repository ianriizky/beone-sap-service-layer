<?php

namespace Ianriizky\BeoneSAPServiceLayer\Services\Api;

use Illuminate\Http\Client\Response;

/**
 * @property \Illuminate\Http\Client\PendingRequest $request
 */
trait ChartOfAccounts
{
    /**
     * Create "/ChartOfAccounts" GET request to the SAP service layer.
     *
     * @param  array|string|null  $query
     * @return \Illuminate\Http\Client\Response
     */
    protected function ChartOfAccounts($query = null): Response
    {
        return $this->request->get('/ChartOfAccounts', $query);
    }
}
