<?php

interface FileItem {
    
    const DEFAULT_ICON = 'file.png';

    public function getId(); // idcko
    public function getName(); // nazov
    public function getIcon(); // ikonka

    public function actionGoTo(); // urlcka prejst na objekt
    public function actionEdit(); // urlcka na formular s upravou
    public function actionDrop(); // urlcka na zmazanie
}

?>
