<?php

error_reporting(E_ALL);

$host = 'localhost';
$user = 'root';
$password = '';
$dbase = 'test';

// pre heslo potom treba doplnit --password heslo

$DIR = 'C:\xampp\mysql\bin\\';
$DUMP = 'mysqldump.exe --host ' . $host . ' --no-create-db --no-data --user ' . $user . ' --databases ' . $dbase;
$TARGET = ' > "D:\SRC\alien.localhost\alien_v2\alien\_setup\structure.sql"';

$CMD = $DIR . $DUMP . $TARGET;

echo '<pre>';
echo $CMD;
$ret = '';
system($CMD, $ret);
echo 'DONE';
