<?php

namespace Ianriizky\BeoneSAPServiceLayer\Services\Api;

use Illuminate\Http\Client\Response;

/**
 * This entity enables you to manipulate 'ChartOfAccounts'.
 * It represents the General Ledger (G/L) accounts in the Finance module. The Chart of Accounts
 * is an index of all G/L accounts that are used by one or more companies. For every G/L account
 * there is an account number, an account description, and information that determines
 * the function of the account.
 *
 * @see https://sap-samples.github.io/smb-summit-hackathon/b1sl.html
 *
 * @property \Illuminate\Http\Client\PendingRequest $request
 */
trait ChartOfAccounts
{
    /**
     * Create "/ChartOfAccounts(:id)" GET request to the SAP service layer.
     *
     * Retrieve all or some selected properties from an instance of
     * 'ChartOfAccounts' with the given id.
     *
     * @param  string  $id
     * @param  array|string|null  $query
     * @return \Illuminate\Http\Client\Response
     */
    protected function getChartOfAccountsId(string $id, $query = null): Response
    {
        return $this->request->get(sprintf('/ChartOfAccounts(\'%s\')', $id), $query);
    }

    /**
     * Create "/ChartOfAccounts" GET request to the SAP service layer.
     *
     * Retrieve a collection of 'ChartOfAccounts' with all or some
     * selected properties in the given order by specifying
     * the given filter condition.
     *
     * @param  array|string|null  $query
     * @return \Illuminate\Http\Client\Response
     */
    protected function getChartOfAccounts($query = null): Response
    {
        return $this->request->get('/ChartOfAccounts', $query);
    }

    /**
     * Create "/ChartOfAccounts" POST request to the SAP service layer.
     *
     * Create an instance of 'ChartOfAccounts' with the given
     * payload of type 'ChartOfAccount' in JSON format.
     *
     * @param  array  $data
     * @return \Illuminate\Http\Client\Response
     */
    protected function postChartOfAccounts(array $data = []): Response
    {
        return $this->request->post('/ChartOfAccounts', $data);
    }

    /**
     * Create "/ChartOfAccounts(:id)" PATCH request to the SAP service layer.
     *
     * Update an instance of 'ChartOfAccounts' with the given
     * payload of type 'ChartOfAccount' in JSON format
     * and with the specified id.
     *
     * @param  string  $id
     * @param  array  $data
     * @return \Illuminate\Http\Client\Response
     */
    protected function patchChartOfAccounts(string $id, array $data = []): Response
    {
        return $this->request->patch(sprintf('/ChartOfAccounts(\'%s\')', $id), $data);
    }

    /**
     * Create "/ChartOfAccounts(:id)" DELETE request to the SAP service layer.
     *
     * Delete an instance of 'ChartOfAccounts' with the specified id.
     *
     * @param  string  $id
     * @param  array  $data
     * @return \Illuminate\Http\Client\Response
     */
    protected function deleteChartOfAccounts(string $id, array $data = []): Response
    {
        return $this->request->delete(sprintf('/ChartOfAccounts(\'%s\')', $id), $data);
    }
}
