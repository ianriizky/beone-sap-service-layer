<?php

namespace Ianriizky\BeoneSAPServiceLayer\Support\Facades;

use Ianriizky\BeoneSAPServiceLayer\Services\SAPServiceLayer as Service;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Http\Client\Response Login(array $data) Create "/Login" POST request to the SAP service layer.
 * @method static \Illuminate\Http\Client\Response ChartOfAccounts(array|string|null $query) Create "/ChartOfAccounts" GET request to the SAP service layer.
 *
 * @see \Ianriizky\BeoneSAPServiceLayer\Services\SAPServiceLayer
 */
class SAPServiceLayer extends Facade
{
    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return Service::class;
    }
}
