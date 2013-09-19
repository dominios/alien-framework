<?php

namespace Alien\Authorization;

use PDO;
use Alien\Alien;

class Group {

    private $id_g;
    private $details;
    private $permissions;

    /**
     * konstruktor
     * @param int idcko
     */
    public function __construct($id) {
        if (self::groupExists($id)) {
            $this->id_g = $id;
            $this->details = $this->getGroupDetails();
        } else {
            // nejaky log - neexistujuca skupina
            //throw ne UnknownGroupException("Group with this ID: $id does not exist.");
        }
    }

    /*     * ******* STATIC METHODS ********************************************************************* */

    /**
     * skontroluje ci neni update a ked tak ho vykona
     */
    public static function update() {
        if (@isset($_POST['newSubmit'])) {
            Group::create();
        } elseif (@$_POST['task'] == 'editGroupSubmit') {
            $group = new Group($_POST['gid']);
            $DBH = Alien::getDatabaseHandler();
            $STH = $DBH->prepare("
                UPDATE " . Alien::getDBPrefix() . "_groups
                SET groupname=:name, description=:descr
                WHERE id_g=:id
                LIMIT 1
            ");
            $STH->bindValue(':name', $_POST['editGroupGroupname']);
            $STH->bindValue(':descr', $_POST['editGroupDescription']);
            $STH->bindValue(':id', $group->id_g);
            if ($STH->execute()) {
                new Notification("Skupina bola úspešne aktualizovaná.", "success");
            } else {
                new Notification("Skupinu sa nepodarilo aktualizovať.", "error");
            }
            if (Alien::getParameter('allowRedirects')) {
                ob_clean();
                $url = '?page=security&action=editGroup&id=' . $group->id_g;
                header("Location: " . $url, true, 301);
                ob_end_flush();
                exit;
            }
        }
    }

    /**
     * zisti ci skupina existuje
     * @param int $id
     * @return bool ci existuje
     */
    public static function groupExists($id) {
        return true;
//        return AlienContent::getInstance()->groupExists($id);
    }

    /**
     * vytvori novu skupinu
     */
    public static function create() {
        Authorization::permissionTest("?page=security&action=viewGroups", array('groups_create'));
        $DBH = Alien::getDatabaseHandler();
        $write = TRUE;
        if (!empty($_POST['gid'])) {
            $write = FALSE;
        } else {
            $STH = $DBH->prepare("SELECT groupname FROM " . Alien::getDBPrefix() . "_groups WHERE groupname=:name LIMIT 1");
            $STH->bindValue('name', $_POST['newGroupname']);
            $STH->execute();
            if ($STH->rowCount()) {
                $write = FALSE;
                new Notification("Tento názov skupiny už existuje, zadať jedinečný názov.", "warning");
            }
            if (!strlen($_POST['newGroupname'])) {
                $write = false;
                new Notification('Je nutné zadať názov novej skupiny.', 'warning');
            }
            if ($write) {
                $STH = $DBH->prepare("INSERT INTO " . Alien::getDBPrefix() . "_groups (groupname,description,date_created) VALUES (:name, :descr, " . time() . ")");
                $STH->bindValue(':name', $_POST['newGroupname']);
                $STH->bindValue(':descr', $_POST['newDescription']);
                if ($STH->execute()) {
                    new Notification("Skupina bola úspešne vytvorená.", "success");
                } else {
                    new Notification("Skupinu sa nepodarilo vytvoriť.", "success");
                }
            }
        }
        if (Alien::getParameter('allowRedirects')) {
            ob_clean();
            $url = '?page=security&action=viewGroups';
            header("Location: " . $url, true, 301);
            ob_end_flush();
            exit;
        }
    }

    /**
     * odstrani skupinu
     * @param int $id identifikator
     */
    public static function drop($id) {

        $group = new Group($id);
        if (sizeof($group->getMembers(true))) {
            new Notification('Nieje možné odstrániť skupinu, ktorá má aktívnych členov.', 'warning');
        } else {
            $DBH = Alien::getDatabaseHandler();
            $STH = $DBH->prepare('DELETE FROM ' . Alien::getDBPrefix() . '_groups WHERE id_g=:i');
            $STH->bindValue(':i', $id, PDO::PARAM_INT);
            if ($STH->execute()) {
                new Notification('Skupina bola odstránená.', 'success');
            } else {
                new Notification('Skupinu sa nepodarilo odstrániť.', 'error');
            }
        }
        if (Alien::getParameter('allowRedirects')) {
            ob_clean();
            $url = '?page=security&action=viewGroups';
            header('Location: ' . $url, true, 301);
            exit;
        }
    }

    /**
     * formular pre novu skupinu
     */
    public static function renderNewForm() {
        Alien::setHeading(skupinyVytvoritNovu);
        echo ('<form method="POST" action=""><fieldset><legend>Nová skupina</legend>');
        echo ('<input type="hidden" name="action" value="createGroup">');
        echo ('<input type="hidden" name=\"gid\">');
        echo ('<table class="noborders">');
        echo ('<tr><td>' . skupinyNazov . ':</td><td><input type="text" name="newGroupname" value="' . @$_POST['newGroupname'] . '" size="20"></td></tr>');
        echo ('<tr><td>' . skupinyPopis . ':</td><td><input type="text" name="newDescription" value="' . @$_POST['newDescription'] . '" size="20"></td></tr>');
        echo ('<tr><td colspan="2"><input type="submit" value="' . skupinyVytvorit . '"></td></tr>');
        echo ('</table>');
        echo ('</fieldset></form>');
    }

    /**
     * formular pre upravu skupiny
     * @param int $id idcko
     * @return void
     */
    public static function renderForm($id) {
        Authorization::permissionTest('?page=security&action=viewGroups', array('GROUP_VIEW'));
        $group = new Group($id);
        Alien::setHeading(skupinyUpravit . ':&nbsp;' . $group->getName());
        echo ('<form method="POST" name="groupForm"><fieldset><legend>Obecné nastavenia skupiny</legend>');
        echo ('<input type="hidden" name="action=" value="updateGroup">');
        echo ('<input type="hidden" name="gid" value="' . $group->getId() . '">');
        echo ('<input type="hidden" name="task" value="editGroupSubmit">');
        echo ('<table>');
        echo ('<tr><td>' . skupinyNazov . ':</td><td><input type="text" name="editGroupGroupname" value="' . $group->getName() . '" size="20"></td></tr>');
        echo ('<tr><td>' . skupinyPopis . ':</td><td><input type="text" name="editGroupDescription" value="' . $group->getDescription() . '" size="20"></td></tr>');
        echo ('<tr><td colspan="2" align="right"><div class="button positive" onCLick="javascript: $(\'form[name=groupForm]\').submit();"><img src="images/icons/save.png"> ' . skupinyUlozitZmeny . '</div></td></tr>');
        echo ('</table>');
        echo ('</fieldset></form>');
        if (Authorization::permissionTest(null, array('GROUP_VIEW'))) {
            $members = $group->getMembers();
            echo ('<br><strong>Členovia:</strong>');
            if (sizeof($members)) {
                foreach ($members as $member) {
                    $cp = '<div style="float: right; display: inline-block; position: relative;">';
                    if (Authorization::permissionTest(null, array('USER_VIEW'))) {
                        $cp .= '<div class="button"><a href="?page=security&amp;action=editUser&amp;id=' . $member->getId() . '" target="_blank"><img src="images/icons/user_go.png" title="zobraziť používateľa"></a></div>';
                    }
                    if (Authorization::permissionTest(null, array('GROUP_EDIT'))) {
                        $cp .= ' <div class="button negative" onClick="javascript: if(confirm(\'Skutočne chcete odstrániť tohoto člena skupiny?\')) { usersRemoveGroupOfUser(' . $group->getId() . ',' . $member->getId() . ',true); }"><img src="images/icons/cross.png" title="odstrániť zo skupiny"></div>';
                    }
                    $cp .='</div>';
                    echo ('<div class="item"><img src="images/icons/user.png"> ID: ' . $member->getId() . ' | ' . $member->getName() . ' | od: ' . $member->getSinceIsMemberOfGroup($group) . $cp . '</div>');
                }
            } else {
                echo ('<div class="item"><img src="images/icons/information.png"> V tejto skupine sa nenachádzajú žiadny používatelia.</div>');
            }
        }
        $cp = '';
        if (!Authorization::permissionTest(null, array('GROUP_VIEW')))
            return;
        echo ('<br><strong>Oprávnenia skupiny:</strong>');
        $permissions = $group->getPermissions(true, false);
        if (!sizeof($permissions)) {
            echo ('<div class="item"><img src="images/icons/information.png"> Skupina nemá žiadne oprávnenia.</div>');
        }
        foreach ($permissions as $permission) {
            if (Authorization::permissionTest(null, array('GROUP_EDIT'))) {
                $cp = '<div style="float: right; display: inline-block; position: relative;">
                        <div class="button negative" onClick="javascript: usersRemovePermissionOfGroup(' . $group->getId() . ',' . $permission->getId() . ',' . $permission->getValue() . ',true);"><img src="images/icons/cross.png"></div>
                    </div>';
            }
            $img = '<img src="images/icons/shield_' . ($permission->getValue() ? 'add' : 'delete') . '.png" title="' . ($permission->getValue() ? 'povolené' : 'zakázané') . '">';
            echo ('<div class="item"> ' . $img . ' ID: ' . $permission->getId() . ' | ' . $permission->getLabel() . ' | ' . $permission->getDescription() . $cp . '</div>');
        }
        // ak nemá potrebné oprávnenia tak vyskoč
        if (!Authorization::permissionTest(null, array('GROUP_EDIT'))) {
            return;
        }
        echo ('<br><strong>Dostupné oprávnenia:</strong>');
        $permissionsId = $group->getPermissions(null, true);
        $allPermissions = Permission::getAllPermissionsList();
        foreach ($allPermissions as $item) {
            if (in_array($item->getId(), $permissionsId))
                continue;
            $cp = '<div style="float: right; display: inline-block; position: relative;">
                    <div class="button" onClick="javascript: usersAddPermissionToGroup(' . $group->getId() . ',' . $item->getId() . ',1,true);"><img src="images/icons/shield_add.png"></div>
                    <div class="button" onClick="javascript: usersAddPermissionToGroup(' . $group->getId() . ',' . $item->getId() . ',0,true);"><img src="images/icons/shield_delete.png"></div>
                </div>';
            echo ('<div class="item"><img src="images/icons/shield.png"> ID: ' . $item->getId() . ' | ' . $item->getLabel() . ' | ' . $item->getDescription() . $cp . '</div>');
        }
    }

    /**
     * zobrazi zoznam skupin
     */
    public static function renderGroupList() {
        Authorization::permissionTest("?page=home", array('GROUP_VIEW'));
        Alien::setHeading(skupinyZoznam);
        $DBH = Alien::getDatabaseHandler();
        $STH = $DBH->prepare('SELECT * FROM ' . Alien::getDBPrefix() . '_groups');
        $STH->setFetchMode(PDO::FETCH_OBJ);
        $STH->execute();
        if (!$STH->rowCount()) {
            echo ('<div class="item"><img src="images/icons/information.png"> ' . skupinyNeexistujuZiadne . '.');
        } else {
            while ($object = $STH->fetch()) {
                $group = new Group($object->id_g);
                $editAction = '?page=security&amp;action=editGroup&amp;id=' . $group->id_g;
                $dropAction = 'javascript: if(confirm(\'Naozaj chcete vymazať túto skupinu?\')) window.location=\'?page=security&amp;action=dropGroup&amp;id=' . $group->id_g . '\'';
                echo ('<div class="item"><img src="images/icons/group.png"> <b>' . $group->getName() . '</b>');
                echo ('<div style="float: right; display: inline-block; position: relative;">');
                if (Authorization::permissionTest(null, array()))
                    echo ('<a href="' . $editAction . '"><img class="button" src="images/icons/group_edit.png" title="Edit group" alt="Edit"></a>');
                if (Authorization::permissionTest(null, array('GROUP_EDIT')))
                    echo ('<a href="' . $dropAction . '"><img class="button negative" src="images/icons/cross.png" title="Delete group" alt="Delete"></a>');
                echo ('</div><br style="clear: right;">');
                echo ('&nbsp;&nbsp;ID: ' . $group->getId() . '&nbsp;|&nbsp;' . skupinyDatumVytvorenia . ': ' . date('d-m-Y H:i', $group->getDateOfCreation()) . '');
                echo ('</div>');
            }
        }
    }

    /*     * ******* SPECIFIC GROUP METHODS ************************************************************* */

    /**
     * zisti ci ma skupina prava na citanie foldra
     * @param int $folder idcko
     * @return boolean
     */
    public function hasFolderReadAccess($folder) {
        $DBH = Alien::getDatabaseHandler();
        $STH = $DBH->prepare('SELECT view FROM ' . Alien::getParameter('db_prefix') . '_folder_group_permissions WHERE id_f=:f && id_g=:g');
        $STH->bindValue(':f', $folder, PDO::PARAM_INT);
        $STH->bindValue(':g', $this->id_g);
        $STH->execute();
        if ($STH->rowCount()) {
            $result = $STH->fetch();
            if ($result['view'] == 1) {
                return true;
            }
        }
        return false;
    }

    /**
     * zisti ci ma skupina prava na upravu foldra
     * @param int $folder idcko
     * @return boolean
     */
    public function hasFolderModifyAccess($folder) {
        $DBH = Alien::getDatabaseHandler();
        $STH = $DBH->prepare('SELECT modify FROM ' . Alien::getParameter('db_prefix') . '_folder_group_permissions WHERE id_f=:f && id_g=:g');
        $STH->bindValue(':f', $folder, PDO::PARAM_INT);
        $STH->bindValue(':g', $this->id_g);
        $STH->execute();
        if ($STH->rowCount()) {
            $result = $STH->fetch();
            if ($result['modify'] == 1) {
                return true;
            }
        }
        return false;
    }

    /**
     * ziska info o skupne
     * @return Array informacie z databazy
     */
    private function getGroupDetails() {
        $DBH = Alien::getDatabaseHandler();
        $STH = $DBH->prepare("SELECT * FROM " . Alien::getDBPrefix() . "_groups WHERE id_g=:id LIMIT 1");
        $STH->bindValue(':id', $this->id_g);
        $STH->execute();
        return $STH->fetch();
    }

    /**
     * zisti ake ma skupina opravnenia
     * @param boolean $includeNegative ci ma zahrnut zakazy
     * @param boolean $onlyId vrati iba id?
     * @return array|\Permission
     */
    public function getPermissions($includeNegative = false, $onlyId = false) {

        if ($this->permissions != null && $onlyId) {
            $retA = Array();
            foreach ($this->permissions as $perm) {
                $retA[] = $perm->getId();
            }
            return $retA;
        }
        if ($this->permissions != null && !$onlyId) {
            return $this->permissions;
        }

        $DBH = Alien::getDatabaseHandler();
        $STH = $DBH->prepare('SELECT id_p, value FROM ' . Alien::getParameter('db_prefix') . '_group_permissions WHERE id_g=:id');
        $STH->bindValue(':id', $this->id_g);
        $STH->setFetchMode(5);
        $STH->execute();
        $arr = array();
        if (!$STH->rowCount()) {
            return $arr;
        }
        while ($obj = $STH->fetch()) {
            $p = new Permission($obj->id_p);
            $p->setValue($obj->value);
            $arr[] = $p;
        }

        if (!$includeNegative) {
            // $arr - pole všetkých oprávnení vrátane negatívnych
            // $newArr - pole po odstránení vymedzujúcich sa oprávnení
            $newArr = Array();
            foreach ($arr as $permissionTest) {
                foreach ($arr as $permissionCompare) {
                    if ($permissionTest->getId() === $permissionCompare->getId()) {
                        if (!$permissionCompare->getValue() || !$permissionTest->getValue()) {
                            continue 2;
                        }
                        if (in_array($permissionCompare, $newArr) || in_array($permissionTest, $newArr)) {
                            continue 2;
                        }
                    }
                }
                $newArr[] = $permissionTest;
            }
            $this->permissions = $arr = $newArr;
        }

        if ($onlyId) {
            $retA = Array();
            foreach ($arr as $i) {
                $retA[] = $i->getId();
            }
            return $retA;
        } else {
            return $arr;
        }
    }

    /**
     * vrati clenov skupiny
     * @param boolean $onlyId iba idcka?
     * @return \User
     */
    public function getMembers($onlyId = false) {
        $DBH = Alien::getDatabaseHandler();
        $STH = $DBH->prepare('SELECT id_u FROM ' . Alien::getDBPrefix() . '_group_members JOIN ' . Alien::getDBPrefix() . '_users USING(id_u) WHERE id_g=:id && deleted!=1 ORDER BY since ASC');
        $STH->bindValue(':id', $this->getId());
        $STH->execute();
        $array = Array();
        while ($fetched = $STH->fetch()) {
            $onlyId ? $array[] = $fetched['id_u'] : $array[] = new User($fetched['id_u']);
        }
        return $array;
    }

    public function getName() {
        return $this->details['groupname'];
    }

    public function getId() {
        return $this->id_g;
    }

    public function getDateOfCreation() {
        return $this->details['date_created'];
    }

    public function getDescription() {
        return $this->details['description'];
    }

    public function renderGroupDetails(User $user = null) {
        echo ('<h5><img src="images/icons/information.png"> ' . informacie . ':</h5>');
        echo ('&nbsp;&nbsp;&nbsp;&nbsp;<strong>' . skupinyNazovSkupiny . ':</strong>&nbsp;' . $this->details['groupname'] . '<br>');
        echo ('&nbsp;&nbsp;&nbsp;&nbsp;<strong>' . skupinyPopis . ':</strong>&nbsp;' . $this->details['description'] . '<br>');
        echo ('&nbsp;&nbsp;&nbsp;&nbsp;<strong>' . skupinyDatumVytvorenia . ':</strong>&nbsp;' . $this->details['date_created'] . '<br>');
        if ($user && $user->isMemberOfGroup($this)) {
            $since = $user->getSinceIsMemberOfGroup($this);
            echo ('&nbsp;&nbsp;&nbsp;&nbsp;<strong>' . homeClenom . ':</strong>&nbsp;' . 'áno' . ' (on: ' . $since . ')' . '<br>');
        } else {
            echo ('&nbsp;&nbsp;&nbsp;&nbsp;<strong>' . homeClenom . ':</strong>&nbsp;' . 'nie' . '<br>');
        }
        echo ('<h5><img src="images/icons/shield.png"> ' . skupinyOpravnenia . ':</h5>');
        if (sizeof($this->getPermissions()) == 0) {
            echo ('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;skupina nemá žiadne oprávnenia');
            return;
        }
        echo ('<ul style="margin-top: -3px; list-style-type: none;">');
        foreach ($this->getPermissions() as $permission) {
            echo ('<li>' . ($permission->getValue() ? '<img src="images/icons/tick.png" style="height: 16px;">' : '<img src="images/icons/cross.png" style="height: 16px;">') . ' ' . $permission->getDescription() . '</li>');
        }
        echo ('</ul>');
    }

}

?>
