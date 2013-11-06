<?php

namespace Alien\Layot;

use Alien\Controllers\BaseController;

$menu = '';

$menu .= '<ul style="margin-left: 280px;">';
foreach ($this->items as $item) {

    $link = '';
    $icon = '<span class="icon icon-' . $item['img'] . '-light"></span>';

    // perm test dorobit !
    $class = '';
    if (stristr(BaseController::getCurrentControllerClass(), $item['controller'])) {
        $class = 'highlight';
    }

    $link .= '<li class="' . $class . '">';
    $link .= '<a href="' . $item['url'] . '" ' . (isset($item['onclick']) ? 'onclick="' . $item['onclick'] . '"' : '') . '>';
    $link .= $icon;
//    $menu .= '<img src="' . Alien::$SystemImgUrl . $item['img'] . '">' . $item['text'];
    $link .= $item['text'] . '</a>';
    $link .= '</li>';

    $menu .= $link;
}
$menu .= '</ul>';

$menu .= '<ul style="float: right; margin-right: 10px;">';
$menu .= '<li class="" style="float: right;"><a href="/alien//logout"><span class="icon icon-logout-light"></span>Odhlásiť</a></li>';
$menu .= '</ul>';

echo $menu;