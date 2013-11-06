<?php

namespace Alien;

use Alien\Authorization\User;
use Alien\Authorization\Group;
use Alien\Authorization\Permission;
use Alien\Controllers\BaseController;

echo '<table class="itemList">';

echo '<tr class="itemHeaderRow">';
echo '<th></th>';
echo '<th>Meno</th>';
echo '<th>Email</th>';
echo '<th>Dátum registrácie</th>';
echo '<th>Posledný prístup</th>';
echo '<th></th>';
echo '</tr>';

foreach ($this->Users as $user) {

    $editAction = BaseController::actionURL('users', 'edit', array('id' => $user->getId()));

    $groups = Array();
    $groupList = $user->getGroups(true);
    foreach ($groupList as $g) {
        $groups[] = $g->getName();
    }
    $groupStr = sizeof($groupList) ? implode(', ', $groups) : 'nie je členom žiadnej skupiny';

    $perms = Array();
    $permList = $user->getPermissions(true);
    foreach ($permList as $p) {
        $perms[] = $p->getLabel();
    }
    $permStr = sizeof($permList) ? implode(', ', $perms) : 'nemá oprávnenia';

    echo '<tr class="itemRow">';
    echo '<td><span class="icon icon-user"></span>'; //ID: ' . $user->getId() . '</td>';
    echo '<td class="itemLabel">' . $user->getLogin() . '</td>';
    echo '<td class="itemDesc">' . $user->getEmail() . '</td>';
    echo '<td class="itemDesc" style="text-align: center;">' . date('d.m.Y H:i:s', $user->getDateRegistered()) . '</td>';
    echo '<td class="itemDesc" style="text-align: center;">' . date('d.m.Y H:i:s', $user->getLastActive()) . '</td>';
    echo '<td class="itemCP">';
    echo '<a href="' . $editAction . '"><span class="icon icon-edit"></span></a>';
//    echo '<span class="icon icon-group" title="' . $groupStr . '"></span>';
//    echo '<span class="icon icon-locked" title="' . $permStr . '"></span>';
//    echo '<span class="icon icon-delete" title="' . $groupStr . '"></span>';
    echo '</td>';
    echo '</tr>';
}

echo '</table>';


//    echo '<tr class="itemRow">';
//    echo '<td><img src="' . Alien::$SystemImgUrl . '/user.png">ID: ' . $user->getId() . '</td>';
//    echo '<td class="itemLabel">' . $user->getLogin() . '</td>';
//    echo '<td class="itemDesc">' . $user->getEmail() . '</td>';
//    echo '<td class="itemDesc" style="text-align: center;">' . date('d.m.Y H:i:s', $user->getDateRegistered()) . '</td>';
//    echo '<td class="itemDesc" style="text-align: center;">' . date('d.m.Y H:i:s', $user->getLastActive()) . '</td>';
//    echo '<td class="itemCP">';
//    echo '<a href="' . $editAction . '"><img src="' . Alien::$SystemImgUrl . '/edit.png"></a>';
//    echo '<img src="' . Alien::$SystemImgUrl . '/group.png" title="' . $groupStr . '">';
//    echo '<img src="' . Alien::$SystemImgUrl . '/locked.png" title="' . $permStr . '">';
//    echo '<img src="' . Alien::$SystemImgUrl . '/delete.png">';
//    echo '</td>';
//    echo '</tr>';
