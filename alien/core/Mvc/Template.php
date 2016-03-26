<?php

namespace Alien\Mvc;

use Alien\Mvc\Component\ComponentInterface;
use Alien\Mvc\Component\Renderable;
use Alien\Stdlib\Exception\IOException;

/**
 * Template rendering tool.
 *
 * Encapsulates <b>View</b> component of <i>MVC</i> design pattern.
 *
 * Template is responsible for handling custom php/html file templates and their variables.
 *
 * @todo consider caching of escaped variables
 */
class Template implements Renderable
{

    /**
     * @var string Path to the template file.
     */
    protected $filename;

    /**
     * @var array Array of template's variables.
     */
    private $variables = [];

    /**
     * @var ComponentInterface[] Array of template's components.
     */
    private $components = [];

    /**
     * @var bool Switch for auto variables escaping (default: <code>true</code>).
     */
    private $autoEscape = true;

    /**
     * @var bool Switch for auto tags stripping from string variables (default: <code>false</code>).
     */
    private $autoStripTags = false;

    /**
     * Template constructor.
     *
     * Requires path to template file to render in argument <code>$filename</code>.
     * Second argument <code>$variables</code> is optional and can contain any variables,
     * which should be binded to the template.
     *
     * @param string $filename path to the template file.
     * @param array $variables [optional] list of automatically binded variables.
     */
    public function __construct($filename, array $variables = [])
    {
        $this->filename = $filename;
        if (count($variables)) {
            $this->variables = $variables;
        }
    }

    /**
     * Executes template file and returns as string.
     *
     * Rendering process consists of opening template file, which automatically has access to
     * all of it's variables. Handing with variables is configured with options <code>$autoEscape</code>
     * and <code>$autoStripTags</code>. Use proper <code>set*</code> method to change behaviour.
     *
     * @return string rendered content.
     * @throws IOException when template file is not set or file does not exists.
     *
     * @todo context-aware escaping
     * @todo write better docs for variables handling
     * @todo consider usage of View Helpers
     */
    public function render()
    {
        $content = '';

        if (!strlen($this->filename)) {
            throw new IOException("Template file is not set.");
        }

        if (file_exists($this->filename)) {
            ob_start();
            /** @noinspection PhpIncludeInspection */
            include $this->filename;
            $content .= ob_get_contents();
            ob_end_clean();
        } else {
            throw new IOException(sprintf('Template file "%s" is missing.', $this->filename));
        }
        return $content;
    }

    /**
     * Executes template file and returns as string.
     *
     * This method is alias of <code>render()</code>.
     *
     * <b>WARNING</b>: PHP auto conversion to string cannot throw an exception!
     *
     * @return string rendered content.
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * Render partial template inside template.
     *
     * <b>NOTE</b>: This method returns instance of <code>Template</code> instead of <code>string</code>.
     * Template is then automatically normally rendered.
     *
     * @param string $template path to the template file which should be rendered.
     * @param array $variables partial template variables.
     * @return Template
     */
    public function partial($template, $variables = null)
    {
        $template = new self($template);
        if (is_array($variables) && sizeof($variables)) {
            foreach ($variables as $k => $v) {
                $template->$k = $v;
            }
        }
        return $template;
    }

    /**
     * Returns template variable by name.
     *
     * * If variable is not set, returns <code>null</code>.
     * * If variable is set and is <code>string</code>, value is filtered based on configuration switches.
     * * If variable is set and is any other type, it is returned as it is.
     *
     * @param string $name variable key.
     * @return mixed
     */
    public function __get($name)
    {
        if (isset($this->variables[$name])) {
            if (is_string($this->variables[$name])) {
                return $this->autoEscape ? $this->escapeValue($this->variables[$name]) : $this->variables[$name];
            } else {
                return $this->variables[$name];
            }
        } else {
            return null;
        }
    }

    /**
     * Binds template variable.
     *
     * @param string $name variable key.
     * @param mixed $value variable value.
     */
    public function __set($name, $value)
    {
        $this->variables[$name] = $value;
    }

    /**
     * Returns escaped variable value.
     *
     * <b>WARNING</b>: in current implementation, only HTML content is escaped!
     * Use manual escaping for JS/CSS/REGEX to prevent XSS attacks!
     *
     * <b>NOTE</b>: This method behaviour will change in future.
     *
     * @param string $value string to escape.
     * @param bool $stripTags [OPTIONAL] set <code>boolean</code> value to override default settings.
     * @param string $allowedTags [OPTIONAL] list of allowed tags (same as for native <code>strip_tags</code> function).
     * @return string escaped string.
     *
     * @todo context-aware escaping!
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
     * Sets automatic variable escaping on or off.
     *
     * @param bool $escape new option value.
     */
    public function setAutoEscape($escape = true)
    {
        $this->autoEscape = (bool)$escape;
    }

    /**
     * Sets automatic HTML tags stripping on or off.
     *
     * @param bool $stripTags new option value.
     * @todo allowed tags
     */
    public function setAutoStripTags($stripTags = false)
    {
        $this->autoStripTags = (bool)$stripTags;
    }

    /**
     * Returns escaped variable value.
     *
     * @param string $value text to escape
     * @param bool $stripTags [OPTIONAL] if should also strip HTML tags.
     * @param string $allowedTags [OPTIONAL] list of allowed HTML tags.
     * @return string escaped value.
     */
    public function escaped($value, $stripTags = null, $allowedTags = '')
    {
        return is_string($value) ? $this->escapeValue($this->variables[$value], (bool)$stripTags, (string)$allowedTags) : $value;
    }

    /**
     * Returns unmodified variable.
     *
     * @param string $name variable key.
     * @return mixed unescaped value.
     */
    public function unescaped($name)
    {
        return $this->variables[$name];
    }

    /**
     * Sets template variable.
     *
     * Alias of <code>__set()</code> method.
     *
     * @param string $key variable name.
     * @param string $value variable value.
     * @deprecated use bindValue instead.
     */
    public function bindVariable($key, $value)
    {
        $this->__set($key, $value);
    }

    /**
     * Binds component to view.
     *
     * @param ComponentInterface $component component.
     */
    public function bindComponent(ComponentInterface $component)
    {
        $this->components[$component->getName()] = $component;
    }

    /**
     * Renders component.
     *
     * @param string $name component name.
     * @return string rendered string.
     */
    public function renderComponent($name)
    {
        return $this->components[$name]->render();
    }

}
