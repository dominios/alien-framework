<?php

namespace Alien\Layuot;

use Alien\Controllers\BaseController;

$menu = '';

foreach ($this->items as $item) {

    $link = '';

    $icon = '<span class="icon icon-' . $item['img'] . '"></span>';

    $class = '';
    if (BaseController::isActionInActionList(BaseController::getActionFromURL($item['url']))) {
        $class .= 'highlight';
    }

    $link .= '<a href="' . $item['url'] . '" class="' . $class . '">';
    $link .= $icon;
    $link .= $item['text'];
    $link .= '</a>';

    $menu .= $link;
}

echo $menu;
