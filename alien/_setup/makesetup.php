<?php

error_reporting(E_ALL);

$host = 'localhost';
$user = 'root';
$password = '';
$dbase = 'test';

// pre heslo potom treba doplnit --password heslo

$DIR = 'C:\xampp\mysql\bin\\';
$DUMP_STRUCT = 'mysqldump.exe --debug-info --host ' . $host . ' --user ' . $user . ' --databases ' . $dbase . ' --no-create-db --add-drop-table --no-data';
$DUMP_DATA = 'mysqldump.exe --debug-info --host ' . $host . ' --user ' . $user . ' --databases ' . $dbase . ' --no-create-db --extended-insert';
$TARGET1 = ' > "D:\SRC\alien.localhost\alien_v2\alien\_setup\structure.sql"';
$TARGET2 = ' > "D:\SRC\alien.localhost\alien_v2\alien\_setup\data.sql"';

$CMD1 = $DIR . $DUMP_STRUCT . $TARGET1;
$CMD2 = $DIR . $DUMP_DATA . $TARGET2;

$ret1 = '';
$ret2 = '';

echo '<pre>';
echo "WORKING...\n";
system($CMD1, $ret1);
echo "STRCTURE DONE\n";
system($CMD2, $ret2);
echo "DATA DONE\n";
