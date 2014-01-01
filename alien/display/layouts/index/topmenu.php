<?php

namespace Alien\Layout;

use Alien\Controllers\BaseController;
use Alien\Authorization\Authorization;

// perm testy dorobit !
?>
<script type="text/javascript">
    $(document).ready(function($) {
        $('#toppanel li').has('.submenu').click(function(ev) {
            ev.stopPropagation();
            $(this).find('.submenu').slideToggle(400, 'easeInOutElastic');
        });

        $(".button.searchSubmit").click(function() {
            $("#searchForm").submit();
        });
    });
</script>
<?

if (!function_exists('topmenuItemToString')) {

    function topmenuItemToString($item) {

        if (!in_array($item['permission'], array('', NULL)) && !Authorization::getCurrentUser()->hasPermission((string) $item['permission'])) {
            return;
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

$menu .= '<ul class="navbar-search navbar-right">';
$menu .= '<li class="navbar-no-hover">';
$menu .= '<form method="POST" action="???" id="searchForm">';
$menu .= '<input type="text" name="searchString" placeholder="Search ...">';
$menu .= '<div class="button searchSubmit"><span class="icon icon-magnifier-light"></span></div>';
$menu .= '</form>';
$menu .= '</li>';
$menu .= '</ul>';

echo $menu;




//$menu .= '<li class="" style="float: right;"><a href="/alien//logout"><span class="icon icon-logout-light"></span>Odhlásiť</a></li>';
