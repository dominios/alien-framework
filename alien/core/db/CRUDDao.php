<?php

namespace Alien\Db;

use Alien\ActiveRecord;

interface CRUDDao {

    public function create(ActiveRecord &$object);

    public function delete(ActiveRecord $record);

    public function update(ActiveRecord $record);

    public function find($id);

    public function getList();

}