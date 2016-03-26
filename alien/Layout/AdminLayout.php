<?php

namespace Alien\Layout;

use Alien\View;
use Alien\Response;
use Alien\Message;
use Alien\Controllers\AbstractController;
use Alien\Models\Authorization\Authorization;

class AdminLayout extends Layout {

    const SRC = 'display/layouts/admin/index.php';
    const useNotifications = true;
    const useConsole = false;

    private $layoutFolder = '/alien/display/layouts/admin';

    private $title = '';
    private $mainMenu = '';
    private $leftTitle = '';
    private $contentLeft = '';
    private $contentMain = '';
    private $floatPanel = '';

    public function __construct() {
        $this->mainMenu = $this->generateTopMenu($this->topmenuItems());
        $this->prependScript('http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js');
        $this->appendScript($this->layoutFolder . '/js/bootstrap.min.js');
        $this->appendScript('/alien/js/jquery-ui.js');
        $this->appendScript('/alien/js/alien.js'); // TODO zbavit sa toho
        $this->appendScript('/alien/js/alien2.js'); // TODO zbavit sa toho
//        $this->appendScript('/alien/plugins/ckeditor/ckeditor.js');
//        $this->appendStylesheet('/alien/display/alien-theme/jquery-ui-1.10.3.custom.css');

        $this->prependStylesheet($this->layoutFolder . '/css/bootstrap.min.css');
        $this->appendStylesheet($this->layoutFolder . '/css/font-awesome.min.css');
        $this->appendStylesheet($this->layoutFolder . '/css/alien-theme.css');

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
        $unread = false; // Message::getUnreadCount(Authorization::getCurrentUser());
        if ($unread) {
            $messagesText .= '<span class="badge badge-circular">' . $unread . '</span>';
        }

        $userSubmenu = array();
        $userSubmenu[] = Array('permission' => null, 'url' => AbstractController::staticActionURL('dashboard', '#'), 'text' => 'Profil', 'img' => 'user');
        $userSubmenu[] = Array('permission' => null, 'url' => AbstractController::staticActionURL('dashboard', 'messages'), 'text' => $messagesText, 'img' => 'email');
        $userSubmenu[] = Array('permission' => null, 'url' => AbstractController::staticActionURL('base', 'logout'), 'text' => 'Odhlásiť', 'img' => 'logout');

        $left = Array();
//        $left[] = Array('permission' => null, 'url' => AbstractController::staticActionURL('dashboard', 'home'), 'text' => 'Dashboard', 'img' => 'dashboard', 'controller' => 'dashboard');
        $left[] = Array('permission' => 'USER_VIEW', 'url' => AbstractController::staticActionURL('user', 'viewList'), 'text' => 'Používatelia', 'img' => 'user', 'controller' => 'users');
        $left[] = Array('permission' => 'GROUP_VIEW', 'url' => AbstractController::staticActionURL('group', 'view'), 'text' => 'Skupiny', 'img' => 'group', 'controller' => 'group');
        $left[] = Array('permission' => null, 'url' => AbstractController::staticActionURL('building', 'view'), 'text' => 'Budova', 'img' => 'home', 'controller' => 'building');
        $left[] = Array('permission' => null, 'url' => AbstractController::staticActionURL('schedule', 'view', array('interval' => 'week')), 'text' => 'Rozvrh', 'img' => 'clock', 'controller' => 'schedule');
        $left[] = Array('permission' => null, 'url' => AbstractController::staticActionURL('course', 'view'), 'text' => 'Kurzy', 'img' => 'book-stack', 'controller' => 'course');
//        $left[] = Array('permission' => null, 'url' => AbstractController::staticActionURL('users', 'viewList'), 'text' => 'Učitelia', 'img' => 'user', 'controller' => 'users');
//        $left[] = Array('permission' => null, 'url' => AbstractController::staticActionURL('users', 'viewList'), 'text' => 'Študenti', 'img' => 'user', 'controller' => 'users');
//        $left[] = Array('permission' => null, 'url' => AbstractController::staticActionURL('test', 'viewList'), 'text' => 'Testy', 'img' => 'note', 'controller' => 'test');

        $right = array();
//        $right[] = Array('permission' => null, 'url' => AbstractController::staticActionURL('dashboard', '#'), 'text' => \Alien\Models\Authorization\Authorization::getCurrentUser()->getEmail(), 'img' => 'user-circle', 'submenu' => $userSubmenu);

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
