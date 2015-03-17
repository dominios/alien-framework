<?php

namespace Alien\Db;

use Alien\DBRecord;

interface CRUDDao {

    public function create(DBRecord &$object);

    public function delete(DBRecord $record);

    public function update(DBRecord $record);

    public function find($id);

    public function getList();

}