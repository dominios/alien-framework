<?php

namespace Alien\Authorization;

use Alien\Controllers\BaseController;

echo '<table class="itemList">';

echo '<tr class="itemHeaderRow">';
echo '<th></th>';
echo '<th>Meno</th>';
echo '<th>Popis</th>';
echo '<th>Dátum vytvorenia</th>';
echo '<th>Počet členov</th>';
echo '<th></th>';
echo '</tr>';

foreach ($this->groups as $group) {

    $editAction = BaseController::actionURL('groups', 'edit', array('id' => $group->getId()));

    echo '<tr class="itemRow">';
    echo '<td><span class="icon icon-group"></span>'; //ID: ' . $user->getId() . '</td>';
    echo '<td class="itemLabel">' . $group->getName() . '</td>';
    echo '<td class="itemDesc">' . $group->getDescription() . '</td>';
    echo '<td class="itemDesc" style="text-align: center;">' . $group->getDateCreated('d.m.Y H:i:s') . '</td>';
    echo '<td class="itemDesc" style="text-align: center;">' . count($group->getMembers()) . '</td>';
    echo '<td class="itemCP">';
    echo '<a href="' . $editAction . '"><span class="icon icon-edit"></span></a>';
    echo '</td>';
    echo '</tr>';
}

echo '</table>';