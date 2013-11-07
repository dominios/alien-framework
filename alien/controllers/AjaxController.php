<?php

namespace Alien\Controllers;

use Alien\Alien;
use Alien\Terminal;
use Alien\Response;
use Alien\Authorization\User;
use Alien\Authorization\Group;
use Alien\Authorization\Permission;
use Alien\View;

class AjaxController extends BaseController {

    /**
     * @param $REQ request pole
     * @return mixed
     * veci v session prepina grid / row layout
     */
    public function displayLayoutType($REQ) {
        $type = $REQ['type'];

        $view = unserialize($_SESSION['SDATA']);

        $view->DisplayLayout = $type;

        return new Response(Response::RESPONSE_OK, Array(
            'result' => $view->renderToString()
                ), __CLASS__ . '::' . __FUNCTION__);
    }

    /**
     * @param $REQ request pole
     * @return string result
     * parser inputu v terminalovom okne
     */
    public function evalConsoleInput($REQ) {
        $action = $REQ['data'];
        $ret = '';

        $ctrl = new TerminalController();

        if (preg_match('/^php\s.*$/', $action)) {
            error_reporting(0);
            $a = explode('php', $action, 2);
            $action = (string) $a[1];
            $ret .= (string) eval("" . $action);
            $ret .= '<br>';
            echo $ret;
            exit;
        }

        if (method_exists($ctrl, $action) && !in_array($action, Array('init_action', 'getContent', '__construct', 'NOP', 'nop'))) {

            $ret .= ('<span class="ConsoleTime">[' . date('d.m.Y H:i:s', time()) . ']</span> <span class="' . Terminal::MESSAGE . '">' . $action . '<br>' . $ctrl->$action() . '</span><br>');
        } else {
            $ret .= ('<span class="ConsoleTime">[' . date('d.m.Y H:i:s', time()) . ']</span> <span class="' . Terminal::ERROR . '">Command <i><b>' . $action . '</b></i> not recognized.</span><br>');
        }

        return new Response(Response::RESPONSE_OK, Array(
            'result' => $ret
                ), __CLASS__ . '::' . __FUNCTION__);
    }

    /**
     * @param $REQ request pole
     * @return string
     * generuje JSON pre dialogove okno - zoznam dosutpnych suborov pre sablony (php, css, ini)
     */
    public function templateShowFileBrowser($REQ) {

        $header = 'Vybrať súbor';
        $ret = '';
        $content = '';
        $dir = 'templates';

        switch ($REQ['type']) {
            case 'php': $pattern = '.*\.php$';
                $img = 'php.png';
                break;
            case 'ini': $pattern = '.*\.ini$';
                $img = 'service.png';
                break;
            case 'css': $pattern = '.*\.css$';
                $img = 'css.png';
                break;
            default: return json_encode(Array('header' => $header, 'content' => 'Invalid request'));
        }

        $ret = '<div class="gridLayout">';
        $dh = opendir($dir);
        if ($dh) {
            while (($file = readdir($dh)) !== false) {
                if (preg_match('/' . $pattern . '/', $file)) {
                    $content .= '<div class="item" onclick="javascript: chooseFile(\'templates/' . $file . '\', \'' . ucfirst($REQ['type']) . '\');">';
                    $content .= '<img src="' . Alien::$SystemImgUrl . '/' . $img . '" style="width: 48px; height: 48px;">';
                    $content .= '<div style="position: absolute; bottom: 5px; width: 100px; text-align: center;">' . $file . '</div>';
                    $content .= '</div>';
                }
            }
            closedir($dh);
        }

        if (!strlen($content)) {
            $content .= 'Žiadne súbory požadovaného typu.';
        }

        $ret .= $content;
        $ret .= '</div>';

        $content .= '<div style="clear: left;"></div>';

        return new Response(Response::RESPONSE_OK, Array(
            'result' => json_encode(Array('header' => $header, 'content' => $ret))
                ), __CLASS__ . '::' . __FUNCTION__);
    }

    /**
     * @param $REQ request pole
     * @return string
     * vrati JSON pre dialogove okno - nahlad suboru
     */
    public function showFilePreview($REQ) {
        $content = '';
        if (Alien::getParameter('useComplexSyntaxHighlighting')) {
            $geshi = new \GeSHi(file_get_contents($REQ['file']), substr($REQ['file'], -3, 3));
            $geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
            $geshi->enable_strict_mode(false);
            $content .= $geshi->parse_code();
        } else {
            $content = highlight_file($REQ['file'], true);
        }
        return new Response(Response::RESPONSE_OK, Array(
            'result' => json_encode(Array('header' => $REQ['file'], 'content' => $content))
                ), __CLASS__ . '::' . __FUNCTION__);
    }

    /**
     * @param $REQ
     * @return ActionResponse
     * vrati JSON pre vyber sablony
     */
    public function pageShowTemplatesBrowser($REQ) {

        $img = 'template.png';

        $templates = ContentTemplate::getTempatesList(true);
        $content = '';
        $content .= '<div class="gridLayout">';
        foreach ($templates as $template) {
            $content .= '<div class="item" onclick="javascript: chooseTemplate(\'' . $template->getId() . '\', \'' . $template->getName() . '\');">';
            $content .= '<img src="' . Alien::$SystemImgUrl . '/' . $img . '" style="width: 48px; height: 48px;">';
            $content .= '<div style="position: absolute; bottom: 5px; width: 100px; text-align: center;">' . $template->getName() . '</div>';
            $content .= '</div>';
        }
        $content .= '</div>';

        return new Response(Response::RESPONSE_OK, Array(
            'result' => json_encode(Array('header' => 'Vybrať šablónu', 'content' => $content))
                ), __CLASS__ . '::' . __FUNCTION__);
    }

    /**
     * @param $REQ
     * @return ActionResponse
     * vrati JSON pre dialog pre priadenie skupiny usera
     */
    public function userShowAddGroupDialog($REQ) {

        if (User::exists($REQ['userId'])) {
            $user = new User($REQ['userId']);
            $activeGroups = $user->getGroups();
            $allGroups = Group::getList();
            $diff = array_diff($allGroups, $activeGroups);

            $ret = '';

            if (!sizeof($diff)) {
                $ret = 'Žiadna ďalšia skupina na pridanie.';
            } else {
                $ret .= '<div class="gridLayout">';
                foreach ($diff as $group) {
                    $partial = new View('display/common/item.php');
                    $partial->icon = 'group';
                    $partial->item = new Group($group);
                    $partial->onClick = 'javascript: window.location="' . \Alien\Controllers\BaseController::actionURL('users', 'addGroup', array('user' => $user->getId(), 'group' => $group)) . '"';
                    $ret .= $partial->renderToString();
                }
                $ret .= '</div>';
            }
            return new Response(Response::RESPONSE_OK, Array(
                'result' => json_encode(Array('header' => 'Vybrať skupinu', 'content' => $ret))
                    ), __CLASS__ . '::' . __FUNCTION__);
        }
    }

    /**
     * @param $REQ
     * @return ActionResponse
     * vrati JSON pre dialog pre priadenie opravenenia usera
     */
    public function userShowAddPermissionDialog($REQ) {

        if (User::exists($REQ['userId'])) {
            $user = new User($REQ['userId']);
            $activePermissions = $user->getPermissions();
            $allPermissions = Permission::getList();
            $diff = array_diff($allPermissions, $activePermissions);

            $ret = '';

            if (!sizeof($diff)) {
                $ret = 'Žiadne ďalšie oprávnenie na pridanie.';
            } else {
                $ret .= '<div class="gridLayout">';
                foreach ($diff as $permission) {
                    $partial = new View('display/common/item.php');
                    $partial->icon = 'shield';
                    $partial->item = new Permission($permission);
                    $partial->onClick = 'javascript: window.location="' . \Alien\Controllers\BaseController::actionURL('users', 'addPermission', array('user' => $user->getId(), 'permission' => $permission)) . '"';
                    $ret .= $partial->renderToString();
                }
                $ret .= '</div>';
            }
            return new Response(Response::RESPONSE_OK, Array(
                'result' => json_encode(Array('header' => 'Vybrať oprávnenie', 'content' => $ret))
                    ), __CLASS__ . '::' . __FUNCTION__);
        }
    }

    /**
     * @param $REQ
     * @return ActionResponse
     * vrati JSON pre dialog pre priadenie skupiny usera
     */
    public function groupShowAddMemberDialog($REQ) {

        if (Group::exists($REQ['groupId'])) {
            $group = new Group($REQ['groupId']);
            $activeMembers = $group->getMembers();
            $allUsers = User::getList();
            $diff = array_diff($allUsers, $activeMembers);

            $ret = '';

            if (!sizeof($diff)) {
                $ret = 'Žiadny ďalší používatelia pre pridanie.';
            } else {
                $ret .= '<div class="gridLayout">';
                foreach ($diff as $user) {
                    $partial = new View('display/common/item.php');
                    $partial->icon = 'user';
                    $partial->item = new User($user);
                    $partial->onClick = 'javascript: window.location="' . \Alien\Controllers\BaseController::actionURL('groups', 'addMember', array('group' => $group->getId(), 'user' => $user)) . '"';
                    $ret .= $partial->renderToString();
                }
                $ret .= '</div>';
            }
            return new Response(Response::RESPONSE_OK, Array(
                'result' => json_encode(Array('header' => 'Vybrať používateľa', 'content' => $ret))
                    ), __CLASS__ . '::' . __FUNCTION__);
        }
    }

    /**
     * @param $REQ
     * @return ActionResponse
     * vrati JSON pre dialog pre priadenie opravenenia usera
     */
    public function groupShowAddPermissionDialog($REQ) {

        if (Group::exists($REQ['groupId'])) {
            $group = new Group($REQ['groupId']);
            $activePermissions = $group->getPermissions();
            $allPermissions = Permission::getList();
            $diff = array_diff($allPermissions, $activePermissions);

            $ret = '';

            if (!sizeof($diff)) {
                $ret = 'Žiadne ďalšie oprávnenie na pridanie.';
            } else {
                $ret .= '<div class="gridLayout">';
                foreach ($diff as $permission) {
                    $partial = new View('display/common/item.php');
                    $partial->icon = 'shield';
                    $partial->item = new Permission($permission);
                    $partial->onClick = 'javascript: window.location="' . \Alien\Controllers\BaseController::actionURL('groups', 'addPermission', array('group' => $group->getId(), 'permission' => $permission)) . '"';
                    $ret .= $partial->renderToString();
                }
                $ret .= '</div>';
            }
            return new Response(Response::RESPONSE_OK, Array(
                'result' => json_encode(Array('header' => 'Vybrať oprávnenie', 'content' => $ret))
                    ), __CLASS__ . '::' . __FUNCTION__);
        }
    }

}
