<?php

namespace Alien\Layot;

use Alien\Alien;
use Alien\Response;
use Alien\Controllers\BaseController;

class IndexLayout extends Layout {

    const SRC = 'display/index.php';
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
        $items[] = Array('permission' => 'SYSTEM_ACCESS', 'url' => BaseController::actionURL('system', 'NOP'), 'text' => 'Systém', 'img' => 'white/service.png', 'controller' => 'home');
        $items[] = Array('permission' => 'CONTENT_VIEW', 'url' => BaseController::actionURL('content', 'browser'), 'text' => 'Obsah', 'img' => 'white/magazine.png', 'controller' => 'content');
        $items[] = Array('permission' => 'USER_VIEW', 'url' => BaseController::actionURL('users', 'viewList'), 'text' => 'Používatelia', 'img' => 'white/user.png', 'controller' => 'users');
        $items[] = Array('permission' => 'GROUP_VIEW', 'url' => BaseController::actionURL('groups', 'viewList'), 'text' => 'Skupiny', 'img' => 'white/group.png', 'controller' => 'groups');
        $items[] = Array('permission' => null, 'url' => '#', 'url' => BaseController::actionURL('', 'logout'), 'text' => 'Odhlásiť', 'img' => 'white/logout.png');
        return $items;
    }

    private function generateTopMenu($menuitems) {
        $menu = '';
        foreach ($menuitems as $item) {
            // perm test dorobit !
            $class = '';
            if (stristr(BaseController::getCurrentControllerClass(), $item['controller'])) {
                $class = 'highlight';
            }
            $menu .= '<a href="' . $item['url'] . '" ' . (isset($item['onclick']) ? 'onclick="' . $item['onclick'] . '"' : '') . ' class="' . $class . '"><img src="' . Alien::$SystemImgUrl . $item['img'] . '">' . $item['text'] . '</a>';
        }
        return $menu;
    }

    private function generateLeftMenu($menuitems) {
        $menu = '';
        foreach ($menuitems as $item) {
            $class = '';
            $action = explode('=', $item['url']);
            $action = $action[1];
            $action = preg_replace('/&(.*)/', '', $action);
            if (BaseController::isActionInActionList(str_replace('?', '', $action))) {
                $class = 'highlight';
            }
            $menu .= '<a href="' . $item['url'] . '" class="' . $class . '"><img src="' . Alien::$SystemImgUrl . '/white/' . $item['img'] . '">' . $item['text'] . '</a>';
        }
        return $menu;
    }

}

