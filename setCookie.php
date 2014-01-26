<?php

error_reporting(0);

setcookie(htmlspecialchars($_GET['key']), htmlspecialchars($_GET['value']), time() + 3600);
