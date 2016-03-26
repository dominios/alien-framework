<?php

namespace Alien\Mvc\Component;

/**
 * Interface Renderable
 *
 * Provides method <code>render()</code> for converting any objects into renderable string.
 *
 * @package Alien\Mvc\Component
 */
interface Renderable
{

    /**
     * Converts object into string and returns it
     *
     * <b>NOTE</b>: This method is equivalent to standard __toString() call except, this method can throw an exception.
     *
     * @return string
     */
    public function render();

}
