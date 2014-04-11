<?php

namespace Alien\Controllers;

use Alien\Application;
use Alien\DBConfig;
use Alien\Models\Authorization\Authorization;
use Alien\Response;
use Alien\View;
use PDO;

class SearchController extends BaseController {

    private $map;

    protected function initialize() {
        parent::initialize();
        $this->map = $this->getMapDefinition();
    }

    private function getMapDefinition() {
        $map = array(
            'entity' => array(
                'user' => array(
                    'table' => DBConfig::table(DBConfig::USERS),
                    'key' => 'login'
                ),
                'group' => array(
                    'table' => DBConfig::table(DBConfig::GROUPS),
                    'key' => 'name'
                ),
                'page' => array(
                    'table' => DBConfig::table(DBConfig::PAGES),
                    'key' => 'name'
                ),
                'template' => array(
                    'table' => DBConfig::table(DBConfig::TEMPLATES),
                    'key' => 'name'
                )
            )
        );
        return $map;
    }

    protected function search() {

        $result = '';

        $entity = $_POST['entity'];
        $value = $_POST['value'];

        $table = $this->map['entity'][$entity]['table'];
        $key = $this->map['entity'][$entity]['key'];

        $dbh = Application::getDatabaseHandler();
        $stm = $dbh->prepare("SELECT * FROM $table WHERE $key LIKE ?;");
        $stm->execute(array('%' . $value . '%'));

        while ($row = $stm->fetch()) {
            $result .= '<pre>';
            $result .= print_r($row, true);
            $result .= '</pre>';
        }

        $view = new View('display/string.php');
        $view->setAutoEscape(false);
        $view->string = $result;

        return new Response(array(
                'Title' => 'Výsledok vyhľadávania:',
                'ContentMain' => $view->__toString()
            )
        );

    }

}