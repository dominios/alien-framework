<?php

namespace Alien\Layot;

use Alien\View;
use Alien\Response;
use Alien\Controllers\BaseController;

class IndexLayout extends Layout {

    const SRC = 'display/layouts/index/index.php';
    const useConsole = true;
    const useNotifications = true;

    private $Title = '';
    private $MainMenu = '';
    private $LeftTitle = '';
    private $ContentLeft = '';
    private $ContentMain = '';

    public function __construct() {
        $this->MainMenu = $this->generateTopMenu($this->topmenuItems());
    }

    public function getPartials() {
        return Array(
            'Title' => $this->Title,
            'MainMenu' => $this->MainMenu,
            'LeftBox' => $this->ContentLeft,
            'MainContent' => $this->ContentMain,
            'LeftTitle' => $this->LeftTitle
        );
    }

    public function handleResponse(Response $response) {
        $data = $response->getData();
        if (isset($data['Title'])) {
            $this->Title = $data['Title'];
        }
        if (isset($data['LeftTitle'])) {
            $this->LeftTitle = $data['LeftTitle'];
        }
        if (isset($data['ContentLeft']) && is_array($data['ContentLeft'])) {
            $this->ContentLeft = $this->generateLeftMenu($data['ContentLeft']);
        }
        if (isset($data['ContentMain'])) {
            $this->ContentMain .= $data['ContentMain'];
        }
        if (isset($data['MainMenu']) && is_array($data['MainMenu'])) {
            $this->MainMenu = $data['MainMenu'];
        }
    }

    private function topmenuItems() {
        $items = Array();
        $items[] = Array('permission' => 'SYSTEM_ACCESS', 'url' => BaseController::actionURL('system', 'NOP'), 'text' => 'Systém', 'img' => 'service', 'controller' => 'system');
        $items[] = Array('permission' => 'CONTENT_VIEW', 'url' => BaseController::actionURL('content', 'browser'), 'text' => 'Obsah', 'img' => 'magazine', 'controller' => 'content');
        $items[] = Array('permission' => 'USER_VIEW', 'url' => BaseController::actionURL('users', 'viewList'), 'text' => 'Používatelia', 'img' => 'user', 'controller' => 'users');
//        $items[] = Array('permission' => 'USER_VIEW', 'url' => BaseController::actionURL('users', 'viewList'), 'text' => 'Používatelia', 'img' => 'white/user.png', 'controller' => 'users');
        $items[] = Array('permission' => 'GROUP_VIEW', 'url' => BaseController::actionURL('groups', 'viewList'), 'text' => 'Skupiny', 'img' => 'group', 'controller' => 'groups');
//        $items[] = Array('permission' => null, 'url' => '#', 'url' => BaseController::actionURL('', 'logout'), 'text' => 'Odhlásiť', 'img' => 'logout');
        return $items;
    }

    private function generateTopMenu($menuitems) {
        $view = new View('display/layouts/index/topmenu.php');
        $view->items = $menuitems;
        return $view->renderToString();
    }

    private function generateLeftMenu($menuitems) {
        $view = new View('display/layouts/index/mainmenu.php');
        $view->items = $menuitems;
        return $view->renderToString();
    }

}

