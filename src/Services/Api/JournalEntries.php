<?php

namespace Ianriizky\BeoneSAPServiceLayer\Services\Api;

use Illuminate\Http\Client\Response;

/**
 * This entity enables you to manipulate 'JournalEntries'.
 * It represents journal transactions.
 *
 * @see https://sap-samples.github.io/smb-summit-hackathon/b1sl.html
 *
 * @property \Illuminate\Http\Client\PendingRequest $request
 */
trait JournalEntries
{
    /**
     * Create "/JournalEntries(:id)" GET request to the SAP service layer.
     *
     * Retrieve all or some selected properties from an instance of
     * 'JournalEntries' with the given id.
     *
     * @param  string  $id
     * @param  array|string|null  $query
     * @return \Illuminate\Http\Client\Response
     */
    protected function getJournalEntriesId(string $id, $query = null): Response
    {
        return $this->request->get(sprintf('/JournalEntries(\'%s\')', $id), $query);
    }

    /**
     * Create "/JournalEntries" GET request to the SAP service layer.
     *
     * Retrieve a collection of 'JournalEntries' with all or some
     * selected properties in the given order by specifying
     * the given filter condition.
     *
     * @param  array|string|null  $query
     * @return \Illuminate\Http\Client\Response
     */
    protected function getJournalEntries($query = null): Response
    {
        return $this->request->get('/JournalEntries', $query);
    }

    /**
     * Create "/JournalEntries" POST request to the SAP service layer.
     *
     * Create an instance of 'JournalEntries' with the given
     * payload of type 'JournalEntry' in JSON format.
     *
     * @param  array  $data
     * @return \Illuminate\Http\Client\Response
     */
    protected function postJournalEntries(array $data = []): Response
    {
        return $this->request->post('/JournalEntries', $data);
    }

    /**
     * Create "/JournalEntries(:id)" PATCH request to the SAP service layer.
     *
     * Update an instance of 'JournalEntries' with the given
     * payload of type 'JournalEntry' in JSON format
     * and with the specified id.
     *
     * @param  string  $id
     * @param  array  $data
     * @return \Illuminate\Http\Client\Response
     */
    protected function patchJournalEntries(string $id, array $data = []): Response
    {
        return $this->request->patch(sprintf('/JournalEntries(\'%s\')', $id), $data);
    }

    /**
     * Create "/JournalEntries(:id)/Cancel" POST request to the SAP service layer.
     *
     * Invoke the method 'Cancel' on this EntitySet with the specified id.
     *
     * @param  string  $id
     * @param  array  $data
     * @return \Illuminate\Http\Client\Response
     */
    protected function postJournalEntriesCancel(string $id, array $data = []): Response
    {
        return $this->request->post(sprintf('/JournalEntries(\'%s\')/Cancel', $id), $data);
    }
}
