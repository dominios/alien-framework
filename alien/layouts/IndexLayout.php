<?php

namespace Alien\Layout;

use Alien\View;
use Alien\Response;
use Alien\Message;
use Alien\Controllers\BaseController;
use Alien\Models\Authorization\Authorization;

class IndexLayout extends Layout {

    const SRC = 'display/layouts/index/index.php';
    const useConsole = true;
    const useNotifications = true;

    private $title = '';
    private $mainMenu = '';
    private $leftTitle = '';
    private $contentLeft = '';
    private $contentMain = '';
    private $floatPanel = '';

    public function __construct() {
        $this->mainMenu = $this->generateTopMenu($this->topmenuItems());
        $this->prependScript('/alien/js/jquery-ui.js');
        $this->prependScript('/alien/js/jquery-1.8.0.min.js');
        $this->appendScript('/alien/js/alien.js');
        $this->appendScript('/alien/js/alien2.js');
        $this->appendScript('/alien/js/tabs.js');
        $this->appendScript('/alien/js/modals.js');
        $this->appendScript('/alien/js/navbar.js');
        $this->appendScript('/alien/plugins/ckeditor/ckeditor.js');
        $this->prependStylesheet('/alien/display/layouts/index/alien2.css');
        $this->prependStylesheet('/alien/display/layouts/index/alien.css');
        $this->prependStylesheet('/alien/display/alien-theme/jquery-ui-1.10.3.custom.css');
        $this->appendStylesheet('/alien/display/icons.css');
        $this->appendStylesheet('/alien/display/badges.css');
        $this->appendStylesheet('/alien/display/forms.css');
        $this->appendStylesheet('/alien/display/modals.css');
        $this->appendStylesheet('/alien/display/alerts.css');
        $this->appendStylesheet('/alien/display/navbar.css');
        $this->appendStylesheet('/alien/display/sidebar.css');
        $this->appendStylesheet('/alien/display/tabs.css');
        $this->appendStylesheet('/alien/display/icons-data.css');
        $this->appendStylesheet('/alien/display/layouts/index/layout.css');
        $this->appendStylesheet('//cdn.datatables.net/1.10.3/css/jquery.dataTables.min.css');
        $this->appendScript('//cdn.datatables.net/1.10.3/js/jquery.dataTables.min.js');
        $this->appendScript('/alien/js/moment-with-locales.js');
//        $this->appendScript('//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.1.1/fullcalendar.min.js');
        $this->appendScript('/alien/js/fullcalendar.min.js');
//        $this->appendStylesheet('//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.1.1/fullcalendar.min.css');
        $this->appendStylesheet('/alien/display/fullcalendar.min.css');
        // TODO: nejako odlisit stylesheety podla typu (stylesheet, print, ...)
//        $this->appendStylesheet('//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.1.1/fullcalendar.print.css');
    }

    public function getPartials() {
        return Array(
            'title' => $this->title,
            'mainMenu' => $this->mainMenu,
            'leftBox' => $this->contentLeft,
            'mainContent' => $this->contentMain,
            'leftTitle' => $this->leftTitle,
            'floatPanel' => $this->floatPanel
        );
    }

    public function handleResponse(Response $response) {
        $data = $response->getData();
        if (isset($data['Title'])) {
            $this->title = $data['Title'];
        }
        if (isset($data['LeftTitle'])) {
            $this->leftTitle = $data['LeftTitle'];
        }
        if (isset($data['ContentLeft']) && is_array($data['ContentLeft'])) {
            $this->contentLeft = $this->generateLeftMenu($data['ContentLeft']);
        }
        if (isset($data['ContentMain'])) {
            $this->contentMain .= $data['ContentMain'];
        }
        if (isset($data['MainMenu']) && is_array($data['MainMenu'])) {
            $this->mainMenu = $data['MainMenu'];
        }
        if (isset($data['FloatPanel'])) {
            $this->floatPanel = $data['FloatPanel'];
        }
    }

    private function topmenuItems() {

        $messagesText = '';
        $messagesText .= 'Správy';
        $unread = Message::getUnreadCount(Authorization::getCurrentUser());
        if ($unread) {
            $messagesText .= '<span class="badge badge-circular">' . $unread . '</span>';
        }

        $userSubmenu = array();
        $userSubmenu[] = Array('permission' => null, 'url' => BaseController::staticActionURL('dashboard', '#'), 'text' => 'Profil', 'img' => 'user');
        $userSubmenu[] = Array('permission' => null, 'url' => BaseController::staticActionURL('dashboard', 'messages'), 'text' => $messagesText, 'img' => 'email');
        $userSubmenu[] = Array('permission' => null, 'url' => BaseController::staticActionURL('base', 'logout'), 'text' => 'Odhlásiť', 'img' => 'logout');

        $left = Array();
        $left[] = Array('permission' => null, 'url' => BaseController::staticActionURL('dashboard', 'home'), 'text' => 'Dashboard', 'img' => 'dashboard', 'controller' => 'dashboard');
        $left[] = Array('permission' => 'USER_VIEW', 'url' => BaseController::staticActionURL('users', 'viewList'), 'text' => 'Používatelia', 'img' => 'user', 'controller' => 'users');
        $left[] = Array('permission' => 'GROUP_VIEW', 'url' => BaseController::staticActionURL('group', 'view'), 'text' => 'Skupiny', 'img' => 'group', 'controller' => 'groups');
        $left[] = Array('permission' => null, 'url' => BaseController::staticActionURL('building', 'view'), 'text' => 'Budova', 'img' => 'home', 'controller' => 'building');
        $left[] = Array('permission' => null, 'url' => BaseController::staticActionURL('course', 'view'), 'text' => 'Kurzy', 'img' => 'book-stack', 'controller' => 'course');
        $left[] = Array('permission' => null, 'url' => BaseController::staticActionURL('users', 'viewList'), 'text' => 'Učitelia', 'img' => 'user', 'controller' => 'users');
        $left[] = Array('permission' => null, 'url' => BaseController::staticActionURL('users', 'viewList'), 'text' => 'Študenti', 'img' => 'user', 'controller' => 'users');
        $left[] = Array('permission' => null, 'url' => BaseController::staticActionURL('test', 'viewList'), 'text' => 'Testy', 'img' => 'note', 'controller' => 'test');

        $right = array();
        $right[] = Array('permission' => null, 'url' => BaseController::staticActionURL('dashboard', '#'), 'text' => \Alien\Models\Authorization\Authorization::getCurrentUser()->getEmail(), 'img' => 'user-circle', 'submenu' => $userSubmenu);

        $menus = array();
        $menus['left'] = $left;
        $menus['right'] = $right;
        return $menus;
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
