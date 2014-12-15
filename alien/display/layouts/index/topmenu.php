<?php

namespace Alien\Layout;

use Alien\Controllers\BaseController;
use Alien\Forms\Input;
use Alien\Models\Authorization\Authorization;

// perm testy dorobit !
?>
    <script type="text/javascript">
        $(document).ready(function ($) {
            $('#toppanel li').has('.submenu').click(function (ev) {
                ev.stopPropagation();
                $(this).find('.submenu').slideToggle(400, 'easeInOutElastic');
            });

            $(".button.searchSubmit").click(function () {
                $("#searchForm").submit();
            });
        });
    </script>
<?

if (!function_exists('\Alien\Layout\topmenuItemToString')) {

    function topmenuItemToString($item) {

        if (!in_array($item['permission'], array('', NULL)) && !Authorization::getCurrentUser()->hasPermission((string) $item['permission'])) {
            return "";
        }

        $link = '';
        $submenu = '';
        $icon = strlen($item['img']) ? '<span class="icon icon-' . $item['img'] . '-light"></span>' : '';
        $href = preg_match('/#$/', $item['url']) ? '#' : $item['url'];

        $class = '';
        if (stristr(BaseController::getCurrentControllerClass(), $item['controller'])) {
            $class = 'highlight';
        }

        $hasSubmenu = is_array($item['submenu']) && sizeof($item['submenu']);

        $link .= '<li ' . ($class != '' ? 'class="' . $class . '"' : '') . '>';
        $link .= '<a href="' . $href . '" ' . (isset($item['onclick']) ? 'onclick="' . $item['onclick'] . '"' : '') . '>';
        $link .= $icon;
        $link .= $item['text'];

        if ($hasSubmenu) {
            $link .= '<span class="icon icon-xs icon-down-triangle-light"></span>';
            $submenu .= '<ul class="submenu">';
            foreach ($item['submenu'] as $j) {
                $submenu .= topmenuItemToString($j);
            }
            $submenu .= '</ul>';
        }

        $link .= '</a>';
        $link .= $submenu;
        $link .= '</li>';
        return $link;
    }

}

$menu = '';

$menu .= '<div class="navbar-toggle" data-target="mainmenu"><span class="icon icon-menu-light"></span></div>';

$menu .= '<nav class="navbar-content navbar-collapsed mainmenu">';

//$menu .= '<header class="navbar-header">';
//$menu .= '<img src="/alien/display/img/alien_logo_white.png" alt="ALiEN">';
//$menu .= '</header>';

$menu .= '<ul>';
foreach ($this->items['left'] as $item) {
    $menu .= topmenuItemToString($item);
}
$menu .= '</ul>';

$menu .= '<ul class="navbar-right">';
foreach ($this->items['right'] as $item) {
    $menu .= topmenuItemToString($item);
}
$menu .= '</ul>';

$menu .= '<ul class="navbar-search navbar-right" id="searchbar">';
$menu .= '<li class="navbar-no-hover">';
$menu .= '<form method="POST" action="/alien/search/search" id="searchForm">';
$menu .= '<input type="hidden" name="action" value="search/search">';
$menu .= '<input type="hidden" name="entity" value="user">';
$menu .= '<input type="text" name="value" placeholder="Search ...">';
$menu .= '<div class="button searchSubmit"><span class="icon icon-magnifier-light"></span></div>';
$menu .= '</form>';
$menu .= '</li>';
$menu .= '</ul>';

$menu .= '</nav>';

echo $menu;
