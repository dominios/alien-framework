<?php

namespace Alien\Forms;

use Alien\Forms\Input\Button;
use Alien\Forms\Input\Checkbox;
use Alien\Forms\Input\Color;
use Alien\Forms\Input\Csrf;
use Alien\Forms\Input\DateTimeLocal;
use Alien\Forms\Input\Hidden;
use Alien\Forms\Input\Password;
use Alien\Forms\Input\Radio;
use Alien\Forms\Input\Select;
use Alien\Forms\Input\Submit;
use Alien\Forms\Input\Text;
use Alien\Forms\Input\Textarea;
use Alien\Forms\Validator\ValidatorException;
use DateTime;
use InvalidArgumentException;

abstract class Input {

    /**
     * If set to true, all inputs are automatically hydrated with data from Input::$hydratorArray
     * @var bool
     */
    protected static $autoHydrate = true;

    /**
     * Array to use for hydration. Null represents default state in which $_POST is used
     * @var null
     */
    protected static $hydratorArray = null;

    /**
     * Input name attribute
     * @var string
     */
    protected $name;

    /**
     * Input label string
     * @var
     */
    protected $label;

    /**
     * Input type attribute
     * @var string
     */
    protected $type;

    /**
     * Input default value attribute
     * @var string
     */
    protected $defaultValue;

    /**
     * Input value
     * @var null|string null if value is not set, otherwise string
     */
    protected $value;

    /**
     * Input size attribute
     * @var int|null
     */
    protected $size;

    /**
     * Input disabled attribute
     * @var bool
     */
    protected $disabled = false;

    /**
     * Input placeholder attribute
     * @var
     */
    protected $placeholder;

    /**
     * Input autocomplete attribute
     * @var bool true/false is analogic to on/off
     */
    protected $autocomplete = true;

    /**
     * Array of css classes of Input
     * @var array
     */
    protected $cssClass = array();

    /**
     * Input style attribute
     * @var string
     */
    protected $cssStyle;

    /**
     * HTML DOM id
     * @var string
     */
    protected $domId;

    /**
     * Array of validators to check Input's value against when validated
     * @var array
     */
    protected $validators = array();

    /**
     * Cached result of last validation result
     * @var null
     */
    private $validationResult = null;

    /**
     * Css class to use to generate bootstripe like icon
     * @var string
     */
    protected $icon;

    /**
     * Error message to display when validation fails
     * @var string
     */
    protected $errorMessage;

    /**
     * If Input is linked to another. Has impact only on render result when default rendering methods are used.
     * @var bool
     */
    protected $linkedTo = false;

    /**
     * Array of Inputs that are linked to this Input
     * @var array
     */
    protected $linkedInputs = array();

    /**
     * Constructs new Input instance with given attributes
     *
     * @param string $name Input name attribute
     * @param string $type Input type attribute
     * @param string $defaultValue Input default value
     * @param string $value current Input value
     * @param int $size Input size attribute
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

    /**
     * Sets autoHydrate
     * @param $bool
     */
    public static function setAutoHydrate($bool) {
        self::$autoHydrate = (bool) $bool;
    }

    /**
     * Sets array to use for hydration
     * @param $array
     * @throws \InvalidArgumentException
     */
    public static function setHydratorArray($array) {
        if (is_array($array)) {
            self::$hydratorArray = $array;
        } else {
            throw new InvalidArgumentException("Cannot use other type then array for hydration.");
        }
    }

    /**
     * Finds value corresponding value in hydrator array and fill Input's value by it
     * @return $this
     */
    public function hydrate() {
        if (self::$autoHydrate) {
            if (is_array(self::$hydratorArray) && array_key_exists($this->name, self::$hydratorArray)) {
                $this->value = (string) self::$hydratorArray[$this->name];
            }
        }
        return $this;
    }

    /**
     * Factory method for CSRF input
     * @return Csrf
     */
    public static function csrf() {
        return new Csrf();
    }

    /**
     * Factory method for hidden input
     * @param string $name
     * @param string|null $value
     * @return Hidden
     */
    public static function hidden($name, $value = null) {
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
    public static function password($name, $defaultValue, $value = null, $size = null) {
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
    public static function color($name, $defaultValue, $value = null) {
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
    public static function text($name, $defaultValue, $value = null, $size = null) {
        $input = new Text($name, $defaultValue, $value, $size);
        return $input;
    }

    /**
     * Factory method for select input
     * @param string $name
     * @return Select
     */
    public static function select($name) {
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
    public static function textarea($name, $defaultValue = null, $value = null) {
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
    public static function checkbox($name, $value, $checked) {
        $input = new Checkbox($name, $value, $checked);
        return $input;
    }

    /**
     * Factory method for radio input
     * @param string $name
     * @param string $value
     * @return Radio
     */
    public static function radio($name, $value) {
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
    public static function button($action, $text, $icon = null) {
        $input = new Button($action, $text, $icon);
        return $input;
    }

    /**
     * Factory method for submit button
     * @param string $name
     * @param string|null $value
     * @return Submit
     */
    public static function submit($name = '', $value = null) {
        $input = new Submit($name, $value);
        return $input;
    }

    public static function dateTimeLocal($name, DateTime $defaultValue = null, DateTime $value = null) {
        $input = new DateTimeLocal($name, $defaultValue, $value);
        return $input;
    }

    /**
     * Gets all HTML common attributes like style, class, autocomplete...
     * @param bool $toString if set to true, returns string to render, otherwise array of attribtues
     * @return array|string
     */
    protected function commonRenderAttributes($toString = false) {
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
        if ($this->domId) {
            $attr[] = 'id="' . $this->domId . '"';
        } else {
            $attr[] = 'id="' . $this->name . '"';
        }

        return $toString ? implode(' ', $attr) : $attr;
    }

    /**
     * Sets size attribute
     * @param int $size
     * @return $this
     */
    public function setSize($size) {
        $this->size = (int) $size;
        return $this;
    }

    /**
     * Sets disabled attribute
     * @param bool $disabled
     * @return $this
     */
    public function setDisabled($disabled) {
        $this->disabled = (bool) $disabled;
        return $this;
    }

    /**
     * Sets placeholder attribute
     * @param string $placeholder
     * @return $this
     */
    public function setPlaceholder($placeholder) {
        $this->placeholder = $placeholder;
        return $this;
    }

    /**
     * Sets autocomplete attribute
     * @param bool $autocomplete
     * @return $this
     */
    public function setAutocomplete($autocomplete) {
        $this->autocomplete = (bool) $autocomplete;
        return $this;
    }

    /**
     * Adds css classes separated by comma
     * @param string $class
     * @param mixed $classes optional comma separated classes
     * @return $this
     */
    public function addCssClass(/** ...  */) {
        for ($i = 0; $i < func_num_args(); $i++) {
            $this->cssClass[] = func_get_arg($i);
        }
        return $this;
    }

    /**
     * Remove css class
     * @param string $cssClass needle
     * @return $this
     */
    public function removeCssClass($cssClass) {
        $this->cssClass = array_diff($this->cssClass, array($cssClass));
        return $this;
    }

    /**
     * Sets style attribute
     * @param string $cssStyle
     * @return $this
     */
    public function setCssStyle($cssStyle) {
        $this->cssStyle = $cssStyle;
        return $this;
    }

    /**
     * Resets style attribute
     */
    public function clearCssStyle() {
        $this->cssStyle = '';
    }

    /**
     * Gets value
     * @return null|string
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * Sets value
     * @param string $value
     * @return $this
     */
    public function setValue($value) {
        $this->value = $value;
        return $this;
    }

    /**
     * Attach validator to chain
     * @param Validator $validator
     * @return $this
     */
    public function addValidator(Validator $validator) {
        $this->validators[] = $validator;
        return $this;
    }

    /**
     * Test value against all attached validators
     * @return bool
     */
    public function validate() {
        if (is_bool($this->getValidationResult())) {
            return $this->getValidationResult();
        } else {
            $ret = true;
            foreach ($this->validators as $validator) {
                try {
                    if ($validator instanceof Validator) {
                        $ret = $ret && $validator->validate($this);
                    }
                } catch (ValidatorException $e) {
                    $this->setErrorMessage($e->getMessage());
                    if ($validator->isChainBreaking()) {
                        $ret = false;
                    }
                }
            }
            $this->setValidationResult($ret);
            return $ret;
        }
    }

    /**
     * Ignore cached validation result and validate again
     * @return bool
     */
    public function revalidate() {
        $this->validationResult = null;
        return $this->validate();
    }

    /**
     * Sets error message
     * @param string $errorMessage
     */
    public function setErrorMessage($errorMessage) {
        $this->errorMessage = $errorMessage;
    }

    /**
     * Attach Input to form
     * @param Form $form
     * @return $this
     */
    public function addToForm(Form $form) {
        $form->addElement($this);
        return $this;
    }

    /**
     * Attach Input to fieldset
     * @param Fieldset $fieldset
     * @return $this
     */
    public function addToFieldset(Fieldset $fieldset) {
        $fieldset->push($this);
        return $this;
    }

    /**
     * Gets name attribute
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Sets name attribute
     * @param string $name
     * @return $this
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * Gets type attribute
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Gets input attribute
     * @return int|null
     */
    public function getSize() {
        return $this->size;
    }

    /**
     * Get placeholder attribute
     * @return string
     */
    public function getPlaceholder() {
        return (string) $this->placeholder;
    }

    /**
     * Cache validation result so there is no need to validate again
     * @param bool $validationResult
     */
    public function setValidationResult($validationResult) {
        $this->validationResult = (bool) $validationResult;
    }

    /**
     * Gets cached validation result
     * @return bool
     */
    public function getValidationResult() {
        return $this->validationResult;
    }

    /**
     * Sets icon class string
     * @param string $icon
     * @return $this
     */
    public function setIcon($icon) {
        $this->icon = $icon;
        return $this;
    }

    /**
     * Gets icon class string
     * @return string
     */
    public function getIcon() {
        return $this->icon;
    }

    /**
     * Sets label
     * @param $label
     * @return $this
     */
    public function setLabel($label) {
        $this->label = $label;
        return $this;
    }

    /**
     * Gets label
     * @return string
     */
    public function getLabel() {
        return (string) $this->label;
    }

    /**
     * Test if input is linked to another input
     * @return bool
     */
    public function isLinked() {
        return $this->linkedTo instanceof Input;
    }

    /**
     * Link to another input
     * @param Input $input
     * @return $this
     */
    public function linkTo(Input $input) {
        $this->linkedTo = $input;
        $input->addLinkedInput($this);
        return $this;
    }

    /**
     * Adds this input to given input's linked array
     * @param Input $input
     */
    private function addLinkedInput(Input $input) {
        $this->linkedInputs[] = $input;
    }

    /**
     * Test if has any linked input
     * @return bool
     */
    public function hasLinkedInputs() {
        return count($this->linkedInputs) > 0;
    }

    /**
     * Gets all linked inputs
     * @return array
     */
    public function getLinkedInputs() {
        return $this->linkedInputs;
    }

    /**
     * @param string $domId
     * @return $this
     */
    public function setDomId($domId) {
        $this->domId = $domId;
        return $this;
    }

    /**
     * @return string
     */
    public function getDomId() {
        return $this->domId;
    }


    /**
     * Converts to HTML representation.
     * @return string
     */
    public abstract function __toString();

}
