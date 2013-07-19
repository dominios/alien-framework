<?php

interface FileItem {
    
    const DEFAULT_ICON = 'file.png';

    public function getId();
    public function getName();
    public function getIcon();
}

?>
