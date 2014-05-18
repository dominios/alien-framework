<?php

namespace Alien;

use Alien\Models\Authorization\Authorization;

ob_start();
require_once 'init.php';

Application::boot();
Authorization::getInstance();
$application = Application::getInstance();
echo $application->run();
