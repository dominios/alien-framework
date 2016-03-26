<?php

namespace Alien;

use Alien\Models\Authorization\User;
use Alien\Models\Authorization\UserInterface;
use PDO;

class Message implements DBRecord {

    private $id;
    private $author;
    private $recipient;
    private $message;
    private $dateSent;
    private $dateSeen;
    private $authorTags;
    private $recipientTags;
    private $deletedByAuthor;
    private $deletedByRecipient;

    public function __construct($id, $row = null) {
        if ($row === null && $id === null) {
            $this->id = null;
            return;
        } elseif ($row === null) {
            $DBH = Application::getDatabaseHandler();
            $Q = $DBH->prepare('SELECT * FROM ' . DBConfig::table(DBConfig::MESSAGES) . ' WHERE id=:id');
            $Q->bindValue(':id', $id, PDO::PARAM_INT);
            $Q->execute();
            if (!$Q->rowCount()) {
                $this->id = null;
                return;
            } else {
                $row = $Q->fetch();
            }
        }

        $this->id = (int) $row['id'];
        $this->author = new User($row['author']);
        $this->recipient = new User($row['recipient']);
        $this->message = $row['message'];
        $this->dateSent = $row['dateSent'];
        $this->dateSeen = $row['dateSeen'];
        $this->authorTags = explode(';', $row['authorTags']);
        $this->recipientTags = explode(';', $row['authorTags']);
        $this->deletedByAuthor = (bool) $row['deletedByAuthor'];
        $this->deletedByRecipient = (bool) $row['deletedByRecipient'];
    }

    public function delete() {
        if ($this->isDeletable()) {
            $DBH = Application::getDatabaseHandler();
            $Q = $DBH->exec('DELETE FROM ' . DBConfig::table(DBConfig::MESSAGES) . ' WHERE id=' . (int) $this->id . ' LIMIT 1');
            return true;
        } else {
            return false;
        }
    }

    public function isDeletable() {
        return $this->deletedByAuthor && $this->deletedByRecipient ? true : false;
    }

    public function update() {
        $DBH = Application::getDatabaseHandler();
        $Q = $DBH->prepare('UPDATE ' . DBConfig::table(DBConfig::MESSAGES) . ' SET authorTags=:at, recipientTags=:rt, dateSeen=:ds, deletedByAuthor=:dba, deletedByRecipient=:dbr WHERE id=:id');
        $Q->bindValue(':id', $this->id, PDO::PARAM_INT);
        $Q->bindValue(':at', implode(';', $this->authorTags), PDO::PARAM_STR);
        $Q->bindValue(':rt', implode(';', $this->recipientTags), PDO::PARAM_STR);
        $Q->bindValue(':ds', $this->dateSeen, PDO::PARAM_INT);
        $Q->bindValue(':dba', $this->deletedByAuthor, PDO::PARAM_INT);
        $Q->bindValue(':dbr', $this->deletedByRecipient, PDO::PARAM_INT);
        return $Q->execute() ? true : false;
    }

    public static function create($initialValues) {
        $DBH = Application::getDatabaseHandler();
        $Q = $DBH->prepare('INSERT INTO ' . DBConfig::table(DBConfig::MESSAGES) . ' (author, recipient, message, dateSent) VALUES (:a, :r, :m, :ds)');
        $Q->bindValue(':a', $initialValues['author'], PDO::PARAM_INT);
        $Q->bindValue(':r', $initialValues['recipient'], PDO::PARAM_INT);
        $Q->bindValue(':m', $initialValues['message'], PDO::PARAM_STR);
        $Q->bindValue(':ds', time(), PDO::PARAM_INT);
        return $Q->execute() ? new Message($DBH->lastInsertId()) : false;
    }

    public static function exists($id) {
        $DBH = Application::getDatabaseHandler();
        $STH = $DBH->prepare('SELECT 1 FROM ' . DBConfig::table(DBConfig::MESSAGES) . ' WHERE id=:i LIMIT 1');
        $STH->bindValue(':i', (int) $id, PDO::PARAM_INT);
        $STH->execute();
        return $STH->rowCount() ? true : false;
    }

    public static function getList($fetch = false) {
        $arr = array();
        $DBH = Application::getDatabaseHandler();
        foreach ($DBH->query('SELECT * FROM ' . DBConfig::table(DBConfig::MESSAGES)) as $R) {
            $arr[] = $fetch ? new Message($R['id'], $R) : $R['id'];
        }
        return $arr;
    }

    public static function getListByRecipient(UserInterface $user, $fetch = false) {
        $arr = array();
        $DBH = Application::getDatabaseHandler();
        foreach ($DBH->query('SELECT * FROM ' . DBConfig::table(DBConfig::MESSAGES) . ' WHERE recipient=' . (int) $user->getId() . ' && deletedByRecipient!=1 ORDER BY id DESC') as $R) {
            $arr[] = $fetch ? new Message($R['id'], $R) : $R['id'];
        }
        return $arr;
    }

    public static function getUnreadCount(UserInterface $user) {
        $DBH = Application::getDatabaseHandler();
        $R = $DBH->query('SELECT COUNT(*) FROM ' . DBConfig::table(DBConfig::MESSAGES) . ' WHERE recipient=' . (int) $user->getId() . ' && dateSeen IS NULL')->fetch();
        return $R['COUNT(*)'];
    }

    public static function getListByAuthor(UserInterface $user, $fetch = false) {
        $arr = array();
        $DBH = Application::getDatabaseHandler();
        foreach ($DBH->query('SELECT * FROM ' . DBConfig::table(DBConfig::MESSAGES) . ' WHERE author=' . (int) $user->getId() . ' && deletedByAuthor!=1 ORDER BY id DESC') as $R) {
            $arr[] = $fetch ? new Message($R['id'], $R) : $R['id'];
        }
        return $arr;
    }

    public function getId() {
        return $this->id;
    }

    public function getAuthor() {
        return $this->author;
    }

    public function isRecipient(UserInterface $user) {
        return $user->getId() == $this->recipient->getId() ? true : false;
    }

    public function isAuthor(UserInterface $user) {
        return $user->getId() == $this->author->getId() ? true : false;
    }

    public function getRecipient() {
        return $this->recipient;
    }

    public function getMessage() {
        return $this->message;
    }

    public function getDateSent($format = null) {
        return $format === null ? $this->dateSent : date($format, $this->dateSent);
    }

    public function getDateSeen($format = null) {
        return $format === null ? $this->dateSeen : date($format, $this->dateSeen);
    }

    public function isSeen() {
        return $this->dateSeen == null ? false : true;
    }

    public function getAuthorTags() {
        return $this->authorTags;
    }

    public function getRecipientTags() {
        return $this->recipientTags;
    }

    public function getDeletedByAuthor() {
        return (bool) $this->deletedByAuthor;
    }

    public function getDeletedByRecipient() {
        return (bool) $this->deletedByRecipient;
    }

    public function setDateSeen($dateSeen) {
        $this->dateSeen = $dateSeen;
    }

    public function setAuthorTags($authorTags) {
        $this->authorTags = $authorTags;
    }

    public function setRecipientTags($recipientTags) {
        $this->recipientTags = $recipientTags;
    }

    public function setDeletedByAuthor($deletedByAuthor) {
        $this->deletedByAuthor = $deletedByAuthor;
    }

    public function setDeletedByRecipient($deletedByRecipient) {
        $this->deletedByRecipient = $deletedByRecipient;
    }

    public function setDeletedByUser(UserInterface $user, $bool) {
        if ($user->getId() == $this->author->getId()) {
            $this->deletedByAuthor = $bool;
        }
        if ($user->getId() == $this->recipient->getId()) {
            $this->deletedByRecipient = $bool;
        }
    }

}
