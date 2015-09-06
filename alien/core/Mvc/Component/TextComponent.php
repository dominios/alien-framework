<?php

namespace Alien\Mvc\Component;

class TextComponent implements ComponentInterface
{

    protected $name;
    protected $content = "";

    function __construct($name, $content = "")
    {
        $this->name = $name;
        $this->content = $content;
    }


    public function getName()
    {
        return $this->name;
    }

    public function render()
    {
        return $this->getContent();
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     * @return TextComponent
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

}