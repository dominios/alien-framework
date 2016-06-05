<?php

namespace Alien\Mvc\Component;


class NavigationComponent implements ComponentInterface
{

    protected $links = [];

    public function __construct(array $links) {
        $this->links = $links;
    }

    public function getName()
    {
        return 'Navigation';
    }

    /**
     * Converts object into string and returns it
     *
     * <b>NOTE</b>: This method is equivalent to standard __toString() call except, this method can throw an exception.
     *
     * @return string
     */
    public function render()
    {
        $ret = "";
        $ret .= "<ul class=\"nav nav-justified\">";
        foreach ($this->links as $name => $link) {
            $ret .= sprintf("<li><a href=\"%s\">%s</a>", $link, $name);
        }
        $ret .= "</ul>";
        return $ret;

    }
}