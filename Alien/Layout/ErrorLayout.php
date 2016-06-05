<?php

namespace Alien\Layout;

use Alien\View;
use Alien\Response;
use Alien\Message;
use Alien\Controllers\AbstractController;
use Alien\Models\Authorization\Authorization;

class ErrorLayout extends AdminLayout {

    public function __construct() {
        parent::__construct();
        $this->appendStylesheet('/Alien/display/layouts/error/error.css');
    }
}