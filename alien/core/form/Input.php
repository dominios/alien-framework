<?php

namespace Alien\Forms;

use Alien\Forms\Input\Button;
use Alien\Forms\Input\Checkbox;
use Alien\Forms\Input\Hidden;
use Alien\Forms\Input\Password;
use Alien\Forms\Input\Radio;
use Alien\Forms\Input\Select;
use Alien\Forms\Input\Submit;
use Alien\Forms\Input\Text;
use Alien\Forms\Input\Textarea;

abstract class Input {

    protected static $autoHydrate = true;
    protected static $hydratorArray = null;
    protected $name;
    protected $label;
    protected $type;
    protected $defaultValue;
    protected $value;
    protected $size;
    protected $disabled = false;
    protected $placeholder;
    protected $autocomplete = true;
    protected $cssClass = array();
    protected $cssStyle;
    protected $validators = array();
    private $validationResult = null;
    protected $icon;
    protected $errorMessage;

    /**
     *
     * @param string $name
     * @param string $type
     * @param string $defaultValue
     * @param string $value
     * @param int $size
     */
    protected function __construct($name, $type, $defaultValue, $value = null, $size = null) {
        if (!is_array(self::$hydratorArray) && sizeof($_POST)) {
            self::$hydratorArray = $_POST;
        }
        $this->name = $name;
        $this->type = $type;
        $this->value = $value;
        $this->size = $size;
        $this->defaultValue = $defaultValue;
        $this->hydrate();
    }

    public static function setAutoHydrate($bool) {
        self::$autoHydrate = (bool) $bool;
    }

    public static function setHydratorArray($array) {
        if (is_array($array)) {
            self::$hydratorArray = $array;
        }
    }

    protected function hydrate() {
        if (self::$autoHydrate) {
            if (is_array(self::$hydratorArray) && array_key_exists($this->name, self::$hydratorArray)) {
                $this->value = (string) self::$hydratorArray[$this->name];
            }
        }
        return $this;
    }

    /**
     *
     * @param string $name
     * @param string $value
     * @return \Alien\Forms\Input
     */
    public static function hidden($name, $value = null) {
        $input = new Hidden($name, $value);
        return $input;
    }

    /**
     *
     * @param string $name
     * @param string $defaultValue
     * @param string $value
     * @param int $size
     * @return \Alien\Forms\Input
     */
    public static function password($name, $defaultValue, $value = null, $size = null) {
        $input = new Password($name, $defaultValue, $value, $size);
        return $input;
    }

    /**
     *
     * @param string $name
     * @param string $defaultValue
     * @param string $value
     * @param int $size
     * @return \Alien\Forms\Input
     */
    public static function text($name, $defaultValue, $value = null, $size = null) {
        $input = new Text($name, $defaultValue, $value, $size);
        return $input;
    }

    /**
     *
     * @param string $name
     * @return Select
     */
    public static function select($name) {
        $input = new Select($name);
        return $input;
    }

    /**
     *
     * @param string $name
     * @param string|null $defaultValue
     * @param string|null $value
     * @return Textarea
     */
    public static function textarea($name, $defaultValue = null, $value = null) {
        $input = new Textarea($name, $defaultValue, $value);
        return $input;
    }

    /**
     *
     * @param string $name
     * @param string $value
     * @param bool $checked
     * @return Checkbox
     */
    public static function checkbox($name, $value, $checked) {
        $input = new Checkbox($name, $value, $checked);
        return $input;
    }

    /**
     *
     * @param string $name
     * @param string $value
     * @return Radio
     */
    public static function radio($name, $value) {
        $input = new Radio($name, $value);
        return $input;
    }

    /**
     *
     * @param string $action
     * @param string $text
     * @param string $icon
     * @return Button
     */
    public static function button($action, $text, $icon = null) {
        $input = new Button($action, $text, $icon);
        return $input;
    }

    /**
     *
     * @param string $name
     * @param string|null $value
     * @return Submit
     */
    public static function submit($name = '', $value = null) {
        $input = new Submit($name, $value);
        return $input;
    }

    protected function commonRenderAttributes($validate = false) {
        $attr = array();
        if (!$this->validate() && sizeof($_POST)) {
            $this->addCssClass('invalid');
            if (strlen($this->errorMessage)) {
                $attr[] = 'data-errorMsg="' . $this->errorMessage . '"';
            }
        }
        if ($this->disabled) {
            $this->addCssClass('disabled');
        }
        if (sizeof($this->cssClass)) {
            $class = 'class="' . implode(' ', array_unique($this->cssClass, SORT_STRING)) . '"';
            $attr[] = $class;
        }
        if ($this->cssStyle != '') {
            $style = 'style="' . $this->cssStyle . '"';
            $attr[] = $style;
        }
        if ($this->disabled === true) {
            $attr[] = 'disabled';
        }
        return $attr;
    }

    public function setSize($size) {
        $this->size = $size;
        return $this;
    }

    public function setDisabled($disabled) {
        $this->disabled = (bool) $disabled;
        return $this;
    }

    public function setPlaceholder($placeholder) {
        $this->placeholder = $placeholder;
        return $this;
    }

    public function setAutocomplete($autocomplete) {
        $this->autocomplete = (bool) $autocomplete;
        return $this;
    }

    public function addCssClass() {
        for ($i = 0; $i < func_num_args(); $i++) {
            $this->cssClass[] = func_get_arg($i);
        }
        return $this;
    }

    public function removeCssClass($cssClass) {
        $this->cssClass = array_diff($this->cssClass, array($cssClass));
        return $this;
    }

    public function setCssStyle($cssStyle) {
        $this->cssStyle = $cssStyle;
        return $this;
    }

    public function clearCssStyle() {
        $this->cssStyle = '';
    }

    public function getValue() {
        return $this->value;
    }

    public function setValue($value) {
        $this->value = $value;
        return $this;
    }

    public function addValidator(Validator $validator) {
        $this->validators[] = $validator;
        return $this;
    }

    public function validate() {
        if (is_bool($this->getValidationResult())) {
            return $this->getValidationResult();
        } else {
            $ret = true;
            foreach ($this->validators as $validator) {
                $ret = $ret && $validator->validate($this);
            }
            $this->setValidationResult($ret);
            return $ret;
        }
    }

    public function setErrorMessage($errorMessage) {
        $this->errorMessage = $errorMessage;
    }

    public function addToForm(Form $form) {
        $form->addElement($this);
        return $this;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function getType() {
        return $this->type;
    }

    public function getSize() {
        return $this->size;
    }

    public function getPlaceholder() {
        return $this->placeholder;
    }

    public function setValidationResult($validationResult) {
        $this->validationResult = (bool) $validationResult;
    }

    public function getValidationResult() {
        return $this->validationResult;
    }

    public function setIcon($icon) {
        $this->icon = $icon;
        return $this;
    }

    public function getIcon() {
        return $this->icon;
    }

    public function setLabel($label) {
        $this->label = $label;
        return $this;
    }

    public function getLabel() {
        return $this->label;
    }
}
