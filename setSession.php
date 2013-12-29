<?php

error_reporting(0);
session_start();

$_SESSION['temp_' . $_GET['key']] = $_GET['value'];
