<?php

namespace Alien\Authorization;

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

foreach ($this->Users as $group) {

    $editAction = BaseController::actionURL('users', 'edit', array('id' => $group->getId()));

    $groups = Array();
    $groupList = $group->getGroups(true);
    foreach ($groupList as $g) {
        $groups[] = $g->getName();
    }
    $groupStr = sizeof($groupList) ? implode(', ', $groups) : 'nie je členom žiadnej skupiny';

    $perms = Array();
    $permList = $group->getPermissions(true);
    foreach ($permList as $p) {
        $perms[] = $p->getLabel();
    }
    $permStr = sizeof($permList) ? implode(', ', $perms) : 'nemá oprávnenia';

    echo '<tr class="itemRow">';
    echo '<td><span class="icon icon-user"></span>'; //ID: ' . $user->getId() . '</td>';
    echo '<td class="itemLabel">' . $group->getLogin() . '</td>';
    echo '<td class="itemDesc">' . $group->getEmail() . '</td>';
    echo '<td class="itemDesc" style="text-align: center;">' . date('d.m.Y H:i:s', $group->getDateRegistered()) . '</td>';
    echo '<td class="itemDesc" style="text-align: center;">' . date('d.m.Y H:i:s', $group->getLastActive()) . '</td>';
    echo '<td class="itemCP">';
    echo '<a href="' . $editAction . '"><span class="icon icon-edit"></span></a>';
    echo '</td>';
    echo '</tr>';
}

echo '</table>';
