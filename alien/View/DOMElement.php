<?php

namespace Alien\View;

class DOMElement implements Renderable
{

    /**
     * Element ID.
     * @var string
     */
    protected $id = '';

    /**
     * Tag name.
     * Default: <code>div</code>.
     * @var string
     */
    protected $name = '';

    /**
     * If is paired element (e.g. has closing tag).
     * Default: <code>true</code>.
     * @var bool
     */
    protected $isPairTag = true;

    /**
     * Element attributes.
     * @var string[]
     */
    protected $attributes = [];

    /**
     * Element CSS classes.
     * @var string[]
     */
    protected $class = [];

    /**
     * Element inner content.
     * @var string
     */
    protected $content = '';

    /**
     * Children elements.
     * @var DOMElement[]
     */
    protected $children = [];

    /**
     * Creates new DOMElement instance.
     * @param string $name element tag name.
     */
    public function __construct($name = 'div')
    {
        $this->name = $name;
    }

    /**
     * @param string $id
     * @return DOMElement
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param boolean $isPairTag
     * @return DOMElement
     */
    public function setIsPairTag($isPairTag)
    {
        $this->isPairTag = $isPairTag;
        return $this;
    }

    /**
     * @param \string[] $attributes
     * @return DOMElement
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * @param $name
     * @param $value
     * @return DOMElement
     */
    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
        return $this;
    }

    /**
     * @return DOMElement
     */
    public function clearAttributes()
    {
        $this->attributes = [];
        return $this;
    }

    /**
     * @param $name
     * @param null $value
     * @return string|DOMElement
     */
    public function attr($name, $value = null)
    {
        if ($value !== null) {
            $this->setAttribute($name, $value);
            return $this;
        } else {
            return $this->attributes[$name];
        }
    }

    /**
     * WARNING: clears all other set classes!
     * @param string $class
     * @return DOMElement
     */
    public function setClass($class)
    {
        $this->class = [$class];
        return $this;
    }

    /**
     * @param $class
     * @return DOMElement
     */
    public function addClass($class)
    {
        $this->class[] = $class;
        return $this;
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasClass($name)
    {
        return in_array($name, $this->class);
    }

    /**
     * @param $class
     * @return DOMElement
     */
    public function toggleClass($class)
    {
        $this->hasClass($class)
            ? $this->removeClass($class)
            : $this->addClass($class);
        return $this;
    }

    /**
     * @param $class
     * @return DOMElement
     */
    public function removeClass($class)
    {
        $this->class = array_diff($this->class, [$class]);
        return $this;
    }

    /**
     * @param string $content
     * @return DOMElement
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @param $element
     * @return DOMElement
     */
    public function append($element)
    {
        $this->children[] = $element;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasChildren()
    {
        return count($this->children) > 0;
    }

    /**
     * @return DOMElement
     */
    public function emptyElement()
    {
        $this->children = [];
        $this->content = '';
        return $this;
    }

    /**
     * Converts object into string and returns it
     *
     * <b>NOTE</b>: This method is equivalent to standard <code>__toString()</code> call except, this method can throw an exception.
     *
     * @return string
     */
    public function render()
    {
        $ret = "";
        if ($this->isPairTag) {
            $attrs = $this->renderAttributes();
            if (strlen($attrs)) {
                $attrs = ' ' . $attrs;
            }
            $ret .= '<' . $this->name . $attrs . '>';
            $ret .= htmlspecialchars($this->content);
            if ($this->hasChildren()) {
                foreach ($this->children as $child) {
                    $ret .= $child->render();
                }
            }
            $ret .= '</' . $this->name . '>';
        } else {
            $ret .= '<' . $this->name;
            $attrs = $this->renderAttributes();
            $ret .= strlen($attrs) ? ' ' . $attrs : '';
            $ret .= '>';
        }
        return $ret;
    }

    /**
     * @return string
     */
    protected function renderAttributes()
    {
        $attrs = [];
        if ($this->id) {
            $attrs[] = sprintf("id=\"%s\"", $this->id);
        }
        if (count($this->class)) {
            $attrs[] = 'class="' . implode(' ', $this->class) . '"';
        }
        if (count($this->attributes)) {
            foreach ($this->attributes as $key => $value) {
                $attrs[] = sprintf("%s=\"%s\"", $key, $value);
            }
        }
        $ret = implode(' ', $attrs);
        return $ret;
    }

}