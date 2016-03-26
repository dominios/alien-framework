<?php

namespace Alien\Db;

use Alien\DBRecord;

interface CRUDDao {

    /**
     * Insert's object into data storage and gives new ID to object.
     *
     * @param DBRecord $object
     * @return DBRecord
     */
    public function create(DBRecord &$object);

    /**
     * Delete object from data storage.
     *
     * @param DBRecord $record
     * @return void
     */
    public function delete(DBRecord $record);

    /**
     * Update object in data storage.
     *
     * @param DBRecord $record
     * @return void
     */
    public function update(DBRecord $record);

    /**
     * Finds object by id and return it or throw exception on error.
     *
     * @param int $id
     * @throws RecordNotFoundException
     * @return DBRecord
     */
    public function find($id);

    /**
     * Returns array of all objects.
     *
     * @return DBRecord[]
     */
    public function getList();

}