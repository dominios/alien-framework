<?php

namespace Alien\Mvc;
use Alien\Exception\IOException;
use Alien\Mvc\Component\ComponentInterface;
use Alien\Mvc\Component\Renderable;

/**
 * Template rendering tool
 *
 * @package Alien\Mvc
 * @todo prerobit uchovananie premennych, mieto magickeho __set zvazit setVariable a delit podla typu
 * @todo osetrene hodnoty teoreticky interne cachovat
 */
class View implements Renderable
{

    /**
     * Path to template file
     * @var string
     */
    protected $script;

    /**
     * @var AbstractController
     * @deprecated
     */
    protected $controller;

    /**
     * Array of template's variables
     * @var array
     */
    private $variables = [];

    /**
     * Array of template's components
     * @var ComponentInterface[]
     */
    private $components = [];

    /**
     * Switch for auto variables escaping
     * @var bool
     */
    private $autoEscape = true;

    /**
     * Switch for auto tags stripping from string variables
     * @var bool
     */
    private $autoStripTags = false;

    /**
     * @param $script string
     * @param AbstractController $controller @deprecated
     */
    public function __construct($script, AbstractController $controller = null)
    {
        $this->script = $script;
        $this->controller = $controller;
    }

    /**
     * Converting to string
     *
     * This method is alias of <code>render()</code>.
     *
     * <b>WARNING</b>: PHP auto conversion to string cannot throw an exception!
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * Renders template into string
     *
     * Rendering process consists of opening template file, which automatically has access to
     * all it's variables.
     * Handing with variables is configured with options <code>$autoEscape</code>
     * and <code>$autoStripTags</code>. Use proper <code>set*</code> method to change behaviour.
     *
     * @return string
     * @throws IOException when template file is not set or does not exists
     *
     * @todo napisat lepsie ako sa spravaju premenne, zvazit viewhelpre a lepsie escapovanie, toto je jedna z najpodstatnejsich metod
     */
    public function render()
    {
        $content = '';

        if(!strlen($this->script)) {
            throw new IOException("Template file is not set");
        }

        if (file_exists($this->script)) {
            ob_start();
            include $this->script;
            $content .= ob_get_contents();
            ob_end_clean();
        } else {
            throw new IOException('Template file "' . $this->script . '" is missing');
        }
        return $content;
    }

    /**
     * Render partial view inside view
     * @param $template string path to template file
     * @param array $variables partial view's variables
     * @return View
     */
    public function partial($template, $variables = null)
    {
        $view = new self($template);
        if (is_array($variables) && sizeof($variables)) {
            foreach ($variables as $k => $v) {
                $view->$k = $v;
            }
        }
        return $view;
    }

    /**
     * @return AbstractController
     * @deprecated
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Returns view variable
     *
     * If variable is not set, returns <code>NULL</code>.
     * If variable is set and is <code>string</code>, value is filtered based on configuration switches.
     * If variable is set and is any other type, it is returned as it is.
     *
     * @param $name name of variable (key)
     * @return mixed
     */
    public function __get($name)
    {
        if (isset($this->variables[$name])) {
            if(is_string($this->variables[$name])) {
                return $this->autoEscape ? $this->escapeValue($this->variables[$name]) : $this->variables[$name];
            } else {
                return $this->variables[$name];
            }
        } else {
            return null;
        }
    }

    /**
     * Sets view variable
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->variables[$name] = $value;
    }

    /**
     * Basic HTML escaping
     * @param $value string to escape
     * @param null|bool $stripTags set <code>TRUE</code> / <code>FALSE</code> to override default setting
     * @param string $allowedTags list of allowed tags (same as for native <code>strip_tags</code> function)
     * @return string escaped string
     * @todo lepsie escapovanie, toto je slabota a nebrani proti XSS
     */
    private function escapeValue($value, $stripTags = null, $allowedTags = '')
    {
        if (!is_string($value)) {
            return $value;
        }
        $value .= "";
        if (is_null($stripTags)) {
            $stripTags = $this->autoStripTags;
        }
        if ($stripTags) {
            $value = strip_tags($value, $allowedTags);
        }
        return htmlentities($value, ENT_COMPAT, 'UTF-8');
    }

    /**
     * Sets auto escape on/off
     * @param bool $escape
     */
    public function setAutoEscape($escape = true)
    {
        $this->autoEscape = (bool)$escape;
    }

    /**
     * Sets auto auto tags stripping on/off
     * @param bool $stripTags
     * @todo povolene tagy
     */
    public function setAutoStripTags($stripTags = false)
    {
        $this->autoStripTags = (bool)$stripTags;
    }

    /**
     * Returns escaped string value
     * @param $value string
     * @param null|bool $stripTags
     * @param string $allowedTags
     * @return string
     */
    public function escaped($value, $stripTags = null, $allowedTags = '')
    {
        return is_string($value) ? $this->escapeValue($this->variables[$value], (bool)$stripTags, (string)$allowedTags) : $value;
    }

    /**
     * Returns unmodified variable
     * @param $name variable name
     * @return mixed
     */
    public function unescaped($name)
    {
        return $this->variables[$name];
    }

    /**
     * Alias of magic <code>__set()</code> method
     * @param $key string variable name
     * @param $value mixed variable value
     */
    public function setVariable($key, $value) {
        $this->__set($key, $value);
    }

    /**
     * Adds component to view
     * @param ComponentInterface $component
     */
    public function addComponent(ComponentInterface $component) {
        $this->components[$component->getName()] = $component;
    }

    /**
     * Renders component
     * @param $name
     * @return string
     */
    public function renderComponent($name) {
        return $this->components[$name]->render();
    }

}
