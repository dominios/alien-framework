<?php

namespace Alien\Filesystem;

interface FileInterface
{
    public function getFilename();

    public function getBasename();

    public function getFileContent();
}