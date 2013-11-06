<?php

namespace Alien;

interface ActiveRecord {

    public function update();

    public function delete();

    public static function create($initialValues);

    public static function exists($id);

    public static function getList($fetch = false);
}
