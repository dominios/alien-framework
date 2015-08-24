<?php

namespace Alien\Layout;

use Alien\Controllers\AbstractController;

$menu = '';

foreach ($this->items as $item) {

    $link = '';

    $icon = '<span class="icon icon-' . $item['img'] . '"></span>';

    $class = '';
    if (AbstractController::isActionInActionList(AbstractController::getActionFromURL($item['url'])) || (isset($item['regex']) && preg_match('/' . $item['regex'] . '/i', $_SERVER['REQUEST_URI']))) {
        $class .= 'highlight';
    }

    $link .= '<a href="' . $item['url'] . '" class="' . $class . '">';
    $link .= $icon;
    $link .= $item['text'];
    $link .= '</a>';

    $menu .= $link;
}

echo $menu;
