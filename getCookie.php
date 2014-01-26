<?php

error_reporting(0);

if ($_GET['key'] == 'PHPSESSID') {
    die;
}

echo $_COOKIE[$_GET['key']];
