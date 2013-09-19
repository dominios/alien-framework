<?

namespace Alien;

class FormValidator {

    public static $instance = null;
    private $erroList = Array();

    private function __construct() {

    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function putError($input, $message) {
        $this->erroList[] = Array('inputName' => $input, 'errorMsg' => $message);
        $this->encode();
    }

    private function encode() {
        $_SESSION['formErrorOutput'] = json_encode($this->erroList);
    }

    public function errorsCount() {
        return (int) sizeof($this->erroList);
    }

}

