<?php

namespace Ianriizky\BeoneSAPServiceLayer\Support\Facades;

use Ianriizky\BeoneSAPServiceLayer\Services\SAPServiceLayer as Service;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Http\Client\Response getChartOfAccountsId(string $id, array|string|null $query = null) Create "/ChartOfAccounts(:id)" GET request to the SAP service layer.
 * @method static \Illuminate\Http\Client\Response getChartOfAccounts(array|string|null $query = null) Create "/ChartOfAccounts" GET request to the SAP service layer.
 * @method static \Illuminate\Http\Client\Response postChartOfAccounts(array $data = []) Create "/ChartOfAccounts" POST request to the SAP service layer.
 * @method static \Illuminate\Http\Client\Response patchChartOfAccounts(string $id, array $data = []) Create "/ChartOfAccounts(:id)" PATCH request to the SAP service layer.
 * @method static \Illuminate\Http\Client\Response deleteChartOfAccounts(string $id, array $data = []) Create "/ChartOfAccounts(:id)" DELETE request to the SAP service layer.
 * @method static \Illuminate\Http\Client\Response getJournalEntriesId(string $id, array|string|null $query = null) Create "/JournalEntries(:id)" GET request to the SAP service layer.
 * @method static \Illuminate\Http\Client\Response getJournalEntries(array|string|null $query = null) Create "/JournalEntries" GET request to the SAP service layer.
 * @method static \Illuminate\Http\Client\Response postJournalEntries(array $data = []) Create "/JournalEntries" POST request to the SAP service layer.
 * @method static \Illuminate\Http\Client\Response patchJournalEntries(string $id, array $data = []) Create "/JournalEntries(:id)" PATCH request to the SAP service layer.
 * @method static \Illuminate\Http\Client\Response postJournalEntriesCancel(string $id, array $data = []) Create "/JournalEntries(:id)/Cancel" POST request to the SAP service layer.
 * @method static \Illuminate\Http\Client\Response Login(array $data) Create "/Login" POST request to the SAP service layer.
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
