<?php

namespace Alien\Layout;

use Alien\View;
use Alien\Response;
use Alien\Message;
use Alien\Controllers\BaseController;
use Alien\Models\Authorization\Authorization;

class ErrorLayout extends AdminLayout {

    public function __construct() {
        parent::__construct();
        $this->appendStylesheet('/alien/display/layouts/error/error.css');
    }
}