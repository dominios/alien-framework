<?php
return array(
    1 => array(
        'label' => 'ROOT',
    ),
    2 => array(
        'label' => 'ALL_FOLDERS',
    ),
    3 => array(
        'label' => 'USER_VIEW',
    ),
    4 => array(
        'label' => 'USER_ADMIN',
    ),
    5 => array(
        'label' => 'GROUP_VIEW',
    ),
    6 => array(
        'label' => 'GROUP_ADMIN'
    ),
);


$permission[1]['label'] = 'ROOT';
$permission[1]['sk'] = 'VSETKY OPRAVNENIA';
$permission[1]['en'] = 'ALL PRIVILEGES';

$permission[2]['label'] = 'ALL_FOLDERS';
$permission[2]['sk'] = 'VSETKY ZLOZKY';
$permission[2]['en'] = 'ALL FOLDERS';

$permission[3]['label'] = 'CONTENT_VIEW';
$permission[3]['sk'] = 'prezerať obsah';
$permission[3]['en'] = 'view content';

$permission[4]['label'] = 'CONTENT_EDIT';
$permission[4]['sk'] = 'upravovať obsah';
$permission[4]['en'] = 'edit content';

$permission[5]['label'] = 'TEMPLATE_EDIT';
$permission[5]['sk'] = 'upravovať šablóny';
$permission[5]['en'] = 'edit templates';

$permission[6]['label'] = 'USER_VIEW';
$permission[6]['sk'] = 'zobrazovať používateľov';
$permission[6]['en'] = 'view users';

$permission[7]['label'] = 'USER_ADMIN';
$permission[7]['sk'] = 'upravovať používateľov';
$permission[7]['en'] = 'edit users';

// toto sa uz nepouziva, miesto toho je len celkovo USER_ADMIN
$permission[8]['label'] = 'USER_GROUPS';
$permission[8]['sk'] = 'nastavovať oprávnenia používateľom';
$permission[8]['en'] = 'manage user\'s groups';

$permission[9]['label'] = 'GROUP_VIEW';
$permission[9]['sk'] = 'zobrazovať skupiny';
$permission[9]['en'] = 'view groups';

$permission[10]['label'] = 'GROUP_EDIT';
$permission[10]['sk'] = 'nastavovať skupiny';
$permission[10]['en'] = 'manage groups';

$permission[11]['label'] = 'SYSTEM_ACCESS';
$permission[11]['sk'] = 'prístup k systémovým nastaveniam';
$permission[11]['en'] = 'access to system settings';
