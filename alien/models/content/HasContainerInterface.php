<?php

namespace Alien\Models\Content;

interface HasContainerInterface {

    /**
     * @return WidgetContainer
     */
    public function getWidgetContainer();

    /**
     * @return void
     */
    public function fetchContainerContent();

    /**
     * @return void
     */
    public function flushContainerContent();

    /**
     * @return bool
     */
    public function isContainerContentFetched();
}