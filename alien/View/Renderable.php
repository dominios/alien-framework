<?php

namespace Alien\View;

/**
 * Interface Renderable
 *
 * Provides method <code>render()</code> for converting any objects into renderable string.
 */
interface Renderable
{

    /**
     * Converts object into string and returns it
     *
     * <b>NOTE</b>: This method is equivalent to standard <code>__toString()</code> call except, this method can throw an exception.
     *
     * @return string
     */
    public function render();

}
