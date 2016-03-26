<?php

namespace Alien\Form;

use Alien\Form\Validator\ValidatorException;
use InvalidArgumentException;

/**
 * Abstract representation of every Input
 */
abstract class Input
{

    /**
     * If set to true, all inputs are automatically hydrated with data from Input::$hydratorArray
     * @var bool
     */
    protected static $autoHydrate = true;

    /**
     * Array to use for hydration. Null represents default state in which $_POST superglobal array is used
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
     * @var array Array of any custom attributes.
     */
    protected $attrs = [];

    /**
     * Constructs new Input instance with given attributes
     *
     * @param string $name Input name attribute
     * @param string $type Input type attribute
     * @param string $defaultValue Input default value
     * @param string $value current Input value
     * @param int $size Input size attribute
     * @todo remove hardcoded dependency on superglobals
     */
    public function __construct($name, $type, $defaultValue, $value = null, $size = null)
    {

        if (!is_array(self::$hydratorArray) && sizeof($_POST)) {
            self::$hydratorArray = $_POST;
        }


        $el = DOMElement::create('<input>');
        $el->attr('name', $name)
            ->attr('type', $type)
            ->attr('value', $value)
            ->attr('size', $size)
        ;

        echo $el;
        die;

        $this->defaultValue = $defaultValue;
        $this->hydrate();
    }

    /**
     * Sets autoHydrate
     * @param $bool
     */
    public static function setAutoHydrate($bool)
    {
        self::$autoHydrate = (bool)$bool;
    }

    /**
     * Sets array to use for hydration
     * @param $array
     * @throws \InvalidArgumentException
     */
    public static function setHydratorArray($array)
    {
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
    public function hydrate()
    {
        if (self::$autoHydrate) {
            if (is_array(self::$hydratorArray) && array_key_exists($this->name, self::$hydratorArray)) {
                $this->value = (string)self::$hydratorArray[$this->name];
            }
        }
        return $this;
    }







    /**
     * Adds css classes separated by comma
     * @param string $class
     * @param mixed $classes optional comma separated classes
     * @return $this
     */
    public function addCssClass(/** ...  */)
    {
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
    public function removeCssClass($cssClass)
    {
        $this->cssClass = array_diff($this->cssClass, array($cssClass));
        return $this;
    }

    /**
     * Resets style attribute
     */
    public function clearCssStyle()
    {
        $this->cssStyle = '';
    }





    /**
     * Attach validator to chain
     * @param Validator $validator
     * @return $this
     */
    public function addValidator(Validator $validator)
    {
        $this->validators[] = $validator;
        return $this;
    }

    /**
     * Test value against all attached validators
     * @return bool
     */
    public function validate()
    {
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
    public function revalidate()
    {
        $this->validationResult = null;
        return $this->validate();
    }

    /**
     * Sets error message
     * @param string $errorMessage
     */
    public function setErrorMessage($errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }




    /**
     * Attach Input to form
     * @param Form $form
     * @return $this
     */
    public function addToForm(Form $form)
    {
        $form->addElement($this);
        return $this;
    }

    /**
     * Attach Input to fieldset
     * @param Fieldset $fieldset
     * @return $this
     */
    public function addToFieldset(Fieldset $fieldset)
    {
        $fieldset->push($this);
        return $this;
    }




    /**
     * Cache validation result so there is no need to validate again
     * @param bool $validationResult
     */
    public function setValidationResult($validationResult)
    {
        $this->validationResult = (bool)$validationResult;
    }

    /**
     * Gets cached validation result
     * @return bool
     */
    public function getValidationResult()
    {
        return $this->validationResult;
    }




    /**
     * Test if input is linked to another input
     * @return bool
     */
    public function isLinked()
    {
        return $this->linkedTo instanceof Input;
    }

    /**
     * Link to another input
     * @param Input $input
     * @return $this
     */
    public function linkTo(Input $input)
    {
        $this->linkedTo = $input;
        $input->addLinkedInput($this);
        return $this;
    }

    /**
     * Adds this input to given input's linked array
     * @param Input $input
     */
    private function addLinkedInput(Input $input)
    {
        $this->linkedInputs[] = $input;
    }

    /**
     * Test if has any linked input
     * @return bool
     */
    public function hasLinkedInputs()
    {
        return count($this->linkedInputs) > 0;
    }

    /**
     * Gets all linked inputs
     * @return array
     */
    public function getLinkedInputs()
    {
        return $this->linkedInputs;
    }

}
