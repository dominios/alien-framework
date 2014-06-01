<?php

namespace Alien\Models\Content;

use Alien\ActiveRecord;
use Alien\Db\CRUDDaoImpl;
use Alien\DBConfig;
use InvalidArgumentException;
use PDO;
use PDOStatement;

class TemplateDao extends CRUDDaoImpl {

    /**
     * @return PDOStatement
     */
    protected function prepareCreateStatement() {
        $conn = $this->getConnection();
        $stmt = $conn->prepare('INSERT INTO ' . DBConfig::table(DBConfig::TEMPLATES) . '
             (folder, name, src)
             VALUES (:folder, :name, :src);');
        $stmt->bindValue(':folder', 0, PDO::PARAM_INT);
        $stmt->bindValue(':name', '', PDO::PARAM_STR);
        $stmt->bindValue(':src', '', PDO::PARAM_STR);
        return $stmt;
    }

    /**
     * @param array $result
     * @return ActiveRecord
     */
    protected function createFromResultSet(array $result) {
        $template = new Template($result['id'], $result);
        return $template;
//        $template->set = $row['id'];
//        $template->name = $row['name'];
//        $template->src = $row['src'];
//        $template->description = $row['description'];
    }

    /**
     * @return PDOStatement
     */
    protected function prepareSelectAllStatement() {
        $conn = $this->getConnection();
        return $conn->prepare("SELECT * FROM " . DBConfig::table(DBConfig::TEMPLATES));
    }

    /**
     * @param ActiveRecord $record
     * @throws \InvalidArgumentException
     * @return PDOStatement
     */
    protected function prepareDeleteStatement(ActiveRecord $record) {
        if (!($record instanceof Template)) {
            throw new InvalidArgumentException("Object must be instance of Template class!");
        }
        $conn = $this->getConnection();
        return $conn->prepare('DELETE FROM ' . DBConfig::table(DBConfig::TEMPLATES) . '  WHERE id=' . $record->getId() . ' LIMIT 1;')->execute();
    }

    /**
     * @param int $id
     * @return PDOStatement
     */
    protected function prepareFindStatement($id) {
        $conn = $this->getConnection();
        $stmt = $conn->prepare("SELECT * FROM " . DBConfig::table(DBConfig::TEMPLATES) . " WHERE id=:id LIMIT 1");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt;
    }

    /**
     * @param ActiveRecord $record
     * @throws \InvalidArgumentException
     * @return PDOStatement
     */
    protected function prepareUpdateStatement(ActiveRecord $record) {
        if (!($record instanceof Template)) {
            throw new InvalidArgumentException("Object must be instance of Template class!");
        }
        $conn = $this->getConnection();
        $stmt = $conn->prepare('UPDATE ' . DBConfig::table(DBConfig::TEMPLATES) . '
            SET name=:name,
            src=:src,
            description=:desc
            WHERE id=:i'
        );

        $stmt->bindValue(':i', $record->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':name', $record->getName(), PDO::PARAM_STR);
        $stmt->bindValue(':src', $record->getSrcURL(), PDO::PARAM_STR);
        $stmt->bindValue(':desc', $record->getDescription(), PDO::PARAM_STR);
        return $stmt;
    }
}