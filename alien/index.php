<?php

namespace Alien;

ob_start();
$content = '';
require_once 'init.php';

Application::boot();
$application = Application::getInstance();
echo $application->run();
