<?php

namespace Alien\Form;

use Alien\Form\Input\Button;
use Alien\Form\Input\Checkbox;
use Alien\Form\Input\Color;
use Alien\Form\Input\Csrf;
use Alien\Form\Input\DateTimeLocal;
use Alien\Form\Input\Hidden;
use Alien\Form\Input\Password;
use Alien\Form\Input\Radio;
use Alien\Form\Input\Select;
use Alien\Form\Input\Submit;
use Alien\Form\Input\Text;
use Alien\Form\Input\Textarea;
use DateTime;

class InputFactory
{

    /**
     * Factory method for CSRF input
     * @return Csrf
     */
    public static function csrf()
    {
        return new Csrf();
    }

    /**
     * Factory method for hidden input
     * @param string $name
     * @param string|null $value
     * @return Hidden
     */
    public static function hidden($name, $value = null)
    {
        $input = new Hidden($name, $value);
        return $input;
    }

    /**
     * Factory method for password input
     * @param string $name
     * @param string $defaultValue
     * @param string|null $value
     * @param int|null $size
     * @return Password
     */
    public static function password($name, $defaultValue, $value = null, $size = null)
    {
        $input = new Password($name, $defaultValue, $value, $size);
        return $input;
    }

    /**
     * Factory method fod color input
     * @param string $name
     * @param string $defaultValue
     * @param string|null $value
     * @return Color
     */
    public static function color($name, $defaultValue, $value = null)
    {
        return new Color($name, $defaultValue, $value, 2);
    }

    /**
     * Factory method for text input
     * @param string $name
     * @param string $defaultValue
     * @param string|null $value
     * @param int|null $size
     * @return Text
     */
    public static function text($name, $defaultValue, $value = null, $size = null)
    {
        $input = new Text($name, $defaultValue, $value, $size);
        return $input;
    }

    /**
     * Factory method for select input
     * @param string $name
     * @return Select
     */
    public static function select($name)
    {
        $input = new Select($name);
        return $input;
    }

    /**
     * Factory method for textarea
     * @param string $name
     * @param string|null $defaultValue
     * @param string|null $value
     * @return Textarea
     */
    public static function textarea($name, $defaultValue = null, $value = null)
    {
        $input = new Textarea($name, $defaultValue, $value);
        return $input;
    }

    /**
     * Factory method for checkbox input
     * @param string $name
     * @param string $value
     * @param bool $checked
     * @return Checkbox
     */
    public static function checkbox($name, $value, $checked)
    {
        $input = new Checkbox($name, $value, $checked);
        return $input;
    }

    /**
     * Factory method for radio input
     * @param string $name
     * @param string $value
     * @return Radio
     */
    public static function radio($name, $value)
    {
        $input = new Radio($name, $value);
        return $input;
    }

    /**
     * Factory method for button element
     * @param string $action
     * @param string $text
     * @param string|null $icon
     * @return Button
     */
    public static function button($action, $text, $icon = null)
    {
        $input = new Button($action, $text, $icon);
        return $input;
    }

    /**
     * Factory method for submit button
     * @param string $name
     * @param string|null $value
     * @return Submit
     */
    public static function submit($name = '', $value = null)
    {
        $input = new Submit($name, $value);
        return $input;
    }

    /**
     * Factory method for date input
     * @param $name
     * @param DateTime|null $defaultValue
     * @param DateTime|null $value
     * @return DateTimeLocal
     */
    public static function dateTimeLocal($name, DateTime $defaultValue = null, DateTime $value = null)
    {
        $input = new DateTimeLocal($name, $defaultValue, $value);
        return $input;
    }


}