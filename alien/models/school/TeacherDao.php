<?php


namespace Alien\Models\School;

use Alien\DBConfig;
use Alien\DBRecord;
use Alien\Models\Authorization\User;
use Alien\Models\Authorization\UserDao;
use DateTime;

class TeacherDao extends UserDao {

    protected function createFromResultSet(array $result) {
        $user = parent::createFromResultSet($result);
        $teacher = unserialize(sprintf(
            'O:%d:"%s"%s',
            strlen('Alien\Models\School\Teacher'),
            'Alien\Models\School\Teacher',
            strstr(strstr(serialize($user), '"'), ':')
        ));
        return $teacher;
    }

}