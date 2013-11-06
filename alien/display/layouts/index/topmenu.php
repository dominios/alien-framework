<?php

namespace Alien\Layot;

use Alien\Controllers\BaseController;

// perm testy dorobit !
?>
<script type="text/javascript">
    $(document).ready(function() {
        $('#toppanel li').has('.submenu').click(function() {
            $(this).find('.submenu').slideToggle(200);
        });
    });
</script>
<?

if (!function_exists('topmenuItemToString')) {

    function topmenuItemToString($item) {

        $link = '';
        $submenu = '';

        $icon = '<span class="icon icon-' . $item['img'] . '-light"></span>';
        $href = preg_match('/#$/', $item['url']) ? '#' : $item['url'];
        $class = '';
        if (stristr(BaseController::getCurrentControllerClass(), $item['controller'])) {
            $class = 'highlight';
        }
        $link .= '<li ' . ($class != '' ? 'class="' . $class . '"' : '') . '>';
        $link .= '<a href="' . $href . '" ' . (isset($item['onclick']) ? 'onclick="' . $item['onclick'] . '"' : '') . '>';
        $link .= $icon;
        $link .= $item['text'];
        if (is_array($item['submenu']) && sizeof($item['submenu'])) {
            $submenu .='<span class="icon icon-xs icon-down-triangle-light"></span>';
            $submenu .= '<div class="submenu"><ul>';
            foreach ($item['submenu'] as $j) {
                $submenu .= topmenuItemToString($j);
            }
            $submenu .= '</ul></div>';
        }
        $link .= '</a>';
        $link .= $submenu;
        $link .= '</li>';
        return $link;
    }

}

$menu = '';

$menu .= '<ul style="margin-left: 280px;">';
foreach ($this->items['left'] as $item) {
    $menu .= topmenuItemToString($item);
}
$menu .= '</ul>';

$menu .= '<ul style="float: right; margin-right: 10px;">';
foreach ($this->items['right'] as $item) {
    $menu .= topmenuItemToString($item);
}
$menu .= '</ul>';

echo $menu;




//$menu .= '<li class="" style="float: right;"><a href="/alien//logout"><span class="icon icon-logout-light"></span>Odhlásiť</a></li>';
