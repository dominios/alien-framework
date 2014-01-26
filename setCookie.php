<?php

error_reporting(0);

if ($_GET['key'] == 'PHPSESSID') {
    die;
}

setcookie(htmlspecialchars($_GET['key']), htmlspecialchars($_GET['value']), time() + 3600);
