<?php

namespace Alien\Controllers;

use Alien\Application;
use Alien\Forms\Content\PageForm;
use Alien\Models\Content\TextItem;
use Alien\View;
use Alien\Response;
use Alien\Notification;
use Alien\Models\Content\Folder;
use Alien\Models\Content\Template;
use Alien\Models\Content\TemplateBlock;
use Alien\Models\Content\Page;
use Alien\Models\Content\Widget;
use Alien\Forms\Form;
use Alien\Forms\Input;
use Alien\Forms\Validator;
use PDO;

class TextItemController extends ContentController {

    protected function viewAll() {
        return $this->viewList('text');
    }
}