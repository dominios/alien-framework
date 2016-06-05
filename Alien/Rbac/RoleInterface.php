<?php
namespace Alien\Rbac;

interface RoleInterface {

    /*
     * @todo zovseobecnit na getRoles a odstranit tieto stupidne parametre
     */
    public function getPermissions($fetch = false, $includeGroups = false);

    /**
     * Make permission test upon user
     *
     * @param array $permissions array of <b>ID</b>'s or <b>label</b>'s of permissions, <b>NOT</b> objects.
     * @param string $LOGIC (optional) logic to use for test, if there are more then one permissions. Must be one of <b>OR</b>, <b>AND</b> or <b>XOR</b> logic function. If none was selected, default is AND.
     * @return boolean <b>true</b> if user has needed permission(s), otherwise <b>false</b>.
     */
    public function hasPermission($permissions, $LOGIC = 'AND');

    /**
     * Returns subject's name
     * @return string
     */
    public function getName();

}