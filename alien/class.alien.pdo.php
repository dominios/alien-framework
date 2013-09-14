<?

class AlienPDO extends PDO {

    private $xmlString;

    public function  __construct ($dsn, $username, $password, $driver_options){
        return parent::__construct($dsn, $username, $password, $driver_options);
    }

    public function query($statement){
        $_SESSION['sqltest'][] = $statement;
        return parent::query($statement);
    }
}