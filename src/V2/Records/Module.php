<?php

namespace Zoho\Crm\V2\Records;

use Zoho\Crm\V2\Client;

/**
 * A class to help querying the records of a specific API module.
 */
class Module
{
    /** @var \Zoho\Crm\V2\Client The client to which the module is linked */
    protected $client;

    /** @var string The name of the module */
    protected $name;

    /**
     * The constructor.
     *
     * @param \Zoho\Crm\V2\Client $client The client to which the module is linked
     * @param string $name The name of the module
     */
    public function __construct(Client $client, string $name)
    {
        $this->client = $client;
        $this->name = $name;
    }

    /**
     * Create a query to list the records of the module.
     *
     * @return ListQuery
     */
    public function newListQuery()
    {
        return new ListQuery($this->client, $this->name);
    }

    /**
     * Create a query to get a specific record by ID.
     *
     * @param string|null $id (optional) The record ID
     * @return GetByIdQuery
     */
    public function newGetByIdQuery(string $id = null)
    {
        $query = new GetByIdQuery($this->client, $this->name);

        if (isset($id)) {
            $query->setId($id);
        }

        return $query;
    }

    /**
     * Create a query to list the deleted records of the module.
     *
     * @return ListDeletedQuery
     */
    public function newListDeletedQuery()
    {
        return new ListDeletedQuery($this->client, $this->name);
    }

    /**
     * Create a query to perform a search among the records of the module.
     *
     * @return SearchQuery
     */
    public function newSearchQuery()
    {
        return new SearchQuery($this->client, $this->name);
    }

    /**
     * Create a query to list the records from another module related to a given record.
     *
     * @return ListRelatedQuery
     */
    public function newListRelatedQuery(): ListRelatedQuery
    {
        return new ListRelatedQuery($this->client, $this->name);
    }

    /**
     * Create a query to insert one or many records.
     *
     * @param array|null $triggers (optional) The triggers to enable
     * @return InsertQuery
     */
    public function newInsertQuery(array $triggers = null): InsertQuery
    {
        $query = new InsertQuery($this->client, $this->name);

        if (isset($triggers)) {
            $query->triggers($triggers);
        }

        return $query;
    }

    /**
     * Create a query to delete a specific record by ID.
     *
     * @param string|null $id (optional) The record ID
     * @return DeleteQuery
     */
    public function newDeleteQuery(string $id = null): DeleteQuery
    {
        $query = new DeleteQuery($this->client, $this->name);

        if (isset($id)) {
            $query->setId($id);
        }

        return $query;
    }

    /**
     * Create a query to retrieve all the module records.
     *
     * @return ListQuery
     */
    public function all()
    {
        return $this->newListQuery()->autoPaginated();
    }

    /**
     * Alias of {@see self::newListDeletedQuery()}.
     *
     * @return ListDeletedQuery
     */
    public function deleted()
    {
        return $this->newListDeletedQuery()->autoPaginated();
    }

    /**
     * Create a query to search records matching criteria.
     *
     * @param string $criteria The search criteria
     * @return SearchQuery
     */
    public function search(string $criteria)
    {
        return $this->newSearchQuery()->param('criteria', $criteria)->autoPaginated();
    }

    /**
     * Create a query to search records with a given field value.
     *
     * @param string $field The name of the field
     * @param string $value The wanted value
     * @return SearchQuery
     */
    public function searchBy(string $field, string $value)
    {
        return $this->search("($field:equals:$value)");
    }

    /**
     * Create a query to list the records from another module related to a given record.
     *
     * @param string $recordId The record ID
     * @param string $relatedModuleName The name of the related module
     * @return ListRelatedQuery
     */
    public function relationsOf(string $recordId, string $relatedModuleName): ListRelatedQuery
    {
        return $this->newListRelatedQuery()
            ->setRecordId($recordId)
            ->setRelatedModule($relatedModuleName)
            ->autoPaginated();
    }

    /**
     * Create a query to list the records related to a given record from another module.
     *
     * Inverse of {@see self::relationsOf()}.
     *
     * @param string $relatedModuleName The name of the related module
     * @param string $recordId The related record ID
     * @return ListRelatedQuery
     */
    public function relatedTo(string $relatedModuleName, string $recordId): ListRelatedQuery
    {
        return $this->client->records
            ->module($relatedModuleName)
            ->relationsOf($recordId, $this->name);
    }

    /**
     * Retrieve a record by its ID.
     *
     * @param string $id The record ID
     * @return Record
     */
    public function find(string $id)
    {
        return $this->newGetByIdQuery($id)->get();
    }

    /**
     * Insert a new record.
     *
     * @param array|Record $record The record to insert
     * @param array|null $triggers (optional) The triggers to enable
     * @return array|null
     */
    public function insert($record, array $triggers = null)
    {
        $response = $this->newInsertQuery($triggers)->addRecord($record)->get();

        // Because we intended to explicitly insert only one record,
        // we want to return an individual response.
        return $response[0] ?? null;
    }

    /**
     * Insert new records.
     *
     * @param iterable $records The records to insert
     * @param array|null $triggers (optional) The triggers to enable
     * @return array[]
     */
    public function insertMany($records, array $triggers = null)
    {
        return $this->newInsertQuery($triggers)->addRecords($records)->get();
    }

    /**
     * Delete a record by ID.
     *
     * @param string $id The ID of the record to delete
     * @return array|null
     */
    public function delete(string $id)
    {
        $response = $this->newDeleteQuery($id)->get();

        // Because we intended to explicitly delete only one record,
        // we want to return an individual response.
        return $response[0] ?? null;
    }
}
