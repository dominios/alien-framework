<?php

echo '<table class="itemList">';

echo '<tr class="itemHeaderRow">';
echo '<th></th>';
echo '<th>Meno</th>';
echo '<th>Email</th>';
echo '<th>Dátum registrácie</th>';
echo '<th>Posledný prístup</th>';
echo '<th></th>';
echo '</tr>';

foreach ($this->users as $user) {

    $editAction = str_replace('%ID%', $user->getId(), $this->editActionPattern);
    $sendMessageAction = str_replace('%ID%', $user->getId(), $this->sendMessagePattern);

    echo '<tr class="itemRow">';
    echo '<td><span class="icon icon-user"></span>'; //ID: ' . $user->getId() . '</td>';
    echo '<td class="itemLabel">' . $user->getLogin() . '</td>';
    echo '<td class="itemDesc">' . $user->getEmail() . '</td>';
    echo '<td class="itemDesc" style="text-align: center;">' . date('d.m.Y H:i:s', $user->getDateRegistered()) . '</td>';
    echo '<td class="itemDesc" style="text-align: center;">' . date('d.m.Y H:i:s', $user->getLastActive()) . '</td>';
    echo '<td class="itemCP">';
    echo '<a href="' . $editAction . '"><span class="icon icon-edit"></span></a>';
    echo '<a href="' . $sendMessageAction . '"><span class="icon icon-message"></span></a>';
    echo '</td>';
    echo '</tr>';
}

echo '</table>';
