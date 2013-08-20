<?php

interface FileItem {
    
    const DEFAULT_ICON = 'file.png';

    public function getId(); // idcko
    public function getName(); // nazov
    public function getIcon(); // ikonka
    public function isBrowseable(); // ci sa zobrazuje v zoznamoch

    public static function exists($id);

    public function actionGoTo(); // urlcka prejst na objekt
    public function actionEdit(); // urlcka na formular s upravou
    public function actionDrop(); // urlcka na zmazanie
}