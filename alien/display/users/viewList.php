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

foreach($this->Users as $user){
            
    $groups = Array();
    $groupList = $user->getGroups(true);
    foreach($groupList as $g){
        $groups[] = $g->getName();
    }
    $groupStr = sizeof($groupList) ? implode(', ',$groups) : 'nie je členom žiadnej skupiny';
    
    $perms = Array();
    $permList = $user->getPermissions(true);
    foreach($permList as $p){
        $perms[] = $p->getLabel();
    }
    $permStr = sizeof($permList) ? implode(', ', $perms) : 'nemá oprávnenia';
    
    echo '<tr class="itemRow">';
    echo '<td><img src="'.Alien::$SystemImgUrl.'/user.png">ID: '.$user->getId().'</td>';
    echo '<td class="itemLabel">'.$user->getName().'</td>';
    echo '<td class="itemDesc">'.$user->getEmail().'</td>';
    echo '<td class="itemDesc" style="text-align: center;">'.date('d.m.Y H:i:s', $user->getDateRegistered()).'</td>';
    echo '<td class="itemDesc" style="text-align: center;">'.date('d.m.Y H:i:s', $user->getLastActive()).'</td>';
    echo '<td class="itemCP">';
        echo '<img src="'.Alien::$SystemImgUrl.'/group.png" title="'.$groupStr.'">';
        echo '<img src="'.Alien::$SystemImgUrl.'/locked.png" title="'.$permStr.'">';
        echo '<img src="'.Alien::$SystemImgUrl.'/edit.png">';
        echo '<img src="'.Alien::$SystemImgUrl.'/delete.png">';
    echo '</td>';
    echo '</tr>';
}

echo '</table>';
?>
