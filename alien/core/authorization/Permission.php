<?php

namespace Alien\Authorization;

class Permission {

    private $id;
    private $description;
    private $value;
    private $label;

    public function __construct($identifier) {

        if (is_string($identifier) && !is_numeric($identifier)) {

            $limit = sizeof(Authorization::$Permissions);
            for ($i = 1; $i <= $limit; $i++) {
                if (Authorization::$Permissions[$i]['label'] == $identifier) {
                    break;
                }
            }
            $permission = Authorization::$Permissions[$i];
            $this->id = $i;
            $this->description = $permission['sk'];
            $this->label = $permission['label'];
        } else {
            $permission = Authorization::$Permissions[$identifier];
            $this->id = $identifier;
            $this->description = $permission['sk'];
            $this->label = $permission['label'];
        }
    }

    /*     * ******* STATIC METHODS ********************************************************************* */

    public static function getList($fetch = false) {
        return self::getAllPermissionsList(!$fetch);
    }

    /**
     * zisti ci existuje
     * @param int $id idcko
     * @return boolean
     */
    public static function exists($id) {
        $p = new Permission($id);
        return !is_null($p->id) ? true : false;
    }

    /**
     * zobrazi zoznam vsetkych
     * @global array $ALIEN
     */
    public static function showPermissionsList() {
        if (!Authorization::getCurrentUser()->hasPermission(38)) {
            new Notification("Prístup zamietnutý.", "error");
            header("Location: ?page=home", false, 301);
            ob_end_flush();
            exit;
        }
        global $ALIEN;
        $ALIEN['HEADER'] = 'Zoznam existujúcich oprávnení';
        $limit = sizeof(Authorization::$Permissions);
        for ($i = 1; $i <= $limit; $i++) {
            $permission = new Permission(Authorization::$Permissions[$i]['label']);
            echo '<div class="item"><img src="images/icons/shield.png"> ID: ' . $permission->getId() . ' | <strong>' . $permission->getLabel() . '</strong> | ' . $permission->getDescription() . '</div>';
        }
    }

    /**
     * vrati zoznam vsetkych
     * @param boolean $onlyId iba idcka?
     * @return \Permission
     */
    public static function getAllPermissionsList($onlyId = false) {
        $array = Array();
        $limit = sizeof(Authorization::$Permissions);
        for ($i = 1; $i <= $limit; $i++) {
            $onlyId ? $array[] = $i : $array[] = new Permission($i);
        }
        return $array;
    }

    /*     * ******* SPECIFIC (by $this->id_p) PERMISSION METHODS *************************************** */

    public function getId() {
        return $this->id;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setValue($value) {
        $this->value = (int) $value;
    }

    public function getValue() {
        return $this->value;
    }

    public function getLabel() {
        return $this->label;
    }

    public function getName() {
        return $this->getLabel();
    }

}

?>
