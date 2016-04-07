<?php

namespace Alien\Mvc\Component;

use Alien\View\Renderable;

/**
 * Interface ComponentInterface
 */
interface ComponentInterface extends Renderable
{

    public function getName();

}