<?php

namespace Alien\Forms;

class Input {

    private static $autoHydrate = true;
    private static $hydratorArray = null;
    private $name;
    private $type;
    private $defaultValue;
    private $value;
    private $size;
    private $disabled = false;
    private $placeholder;
    private $autocomplete = true;
    private $cssClass = array();
    private $cssStyle;
    private $validators = array();
    private $icon;
    private $errorMessage;

    /**
     *
     * @param string $name
     * @param string $type
     * @param string $defaultValue
     * @param string $value
     * @param int $size
     */
    private function __construct($name, $type, $defaultValue, $value = null, $size = null) {
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

    private function hydrate() {
        if (self::$autoHydrate) {
            if (is_array(self::$hydratorArray) && key_exists($this->name, self::$hydratorArray)) {
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
        $input = new self($name, 'hidden', null, $value);
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
        $input = self::text($name, $defaultValue, $value, $size);
        $input->type = 'password';
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
        $input = new self($name, 'text', $defaultValue, $value, $size);
        return $input;
    }

    public static function select() {

    }

    public static function textarea() {

    }

    public static function checkbox() {

    }

    public static function radio() {

    }

    /**
     *
     * @param string $action
     * @param string $text
     * @param string $icon
     * @return \self
     */
    public static function button($action, $text, $icon = null) {
        $input = new self('', 'button', null, $action, null);
        $input->icon = $icon;
        $input->placeholder = $text;
        $input->addCssClass('button');
        return $input;
    }

    public static function submit() {

    }

    public function __toString() {
        $ret = '';

        $attr = array();

        if ($this->type == 'button' && $this->disabled) {
            $this->addCssClass('disabled');
        }

        if (!$this->validate() && sizeof($_POST)) {
            $this->addCssClass('invalid');
            if (strlen($this->errorMessage)) {
                $attr[] = 'data-errorMsg="' . $this->errorMessage . '"';
            }
        }

        if (sizeof($this->cssClass)) {
            $class = 'class="' . implode(' ', array_unique($this->cssClass, SORT_STRING)) . '"';
            $attr[] = $class;
        }

        if ($this->cssStyle != '') {
            $style = 'style="' . $this->cssStyle . '"';
            $attr[] = $style;
        }

        if ($this->autocomplete === false) {
            $attr[] = 'autocomplete="off"';
        }

        if ($this->disabled === true) {
            $attr[] = 'disabled';
        }

        switch ($this->type) {
            case 'hidden':
                $ret .= '<input type="hidden" name="' . $this->name . '" value="' . $this->value . '">';
                break;
            case 'text':
                $ret .= '<input type="text" name="' . $this->name . '" value="' . $this->value . '" ' . implode(' ', $attr) . '>';
                break;
            case 'password':
                $ret .= '<input type="password" name="' . $this->name . '" value="' . $this->value . '" ' . implode(' ', $attr) . '>';
                break;
            case 'button':
                $icon = strlen($this->icon) ? '<span class="icon ' . $this->icon . ($this->disabled ? '' : '-light') . '"></span>' : '';
                if (preg_match('/javascript/', $this->value)) {
                    $attr[] = 'onClick="' . ( $this->disabled ? 'javascript: return false;' : $this->value ) . '"';
                    $attr[] = 'href="#"';
                } else {
                    $attr[] = 'href="' . ( $this->disabled ? '#' : $this->value ) . '"';
                }
                $ret .= '<a ' . implode(' ', $attr) . '>' . $icon . $this->placeholder . '</a>';
                break;
        }
        return $ret;
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

    public function addValidator(Validator $validator) {
        $this->validators[] = $validator;
        return $this;
    }

    public function validate() {
        $ret = true;
        foreach ($this->validators as $validator) {
            $ret = $ret && $validator->validate($this);
        }
        return $ret;
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

}
