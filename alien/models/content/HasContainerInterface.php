<?php

namespace Alien\Models\Content;

interface HasContainerInterface {

    /**
     * @return WidgetContainer
     */
    public function getWidgetContainer();

}