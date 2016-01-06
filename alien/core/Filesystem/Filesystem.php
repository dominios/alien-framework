<?php

namespace Alien\Filesystem;

use Alien\Stdlib\Exception\IOException;
use DirectoryIterator;
use SeekableIterator;
use SplFileInfo;

class Filesystem extends SplFileInfo implements SeekableIterator
{

    /**
     * List of file names in current directory.
     * @var string[]
     */
    private $files = [];

    /**
     * Current working directory.
     * @var DirectoryIterator
     */
    private $cwd;

    /**
     * Creates new instance of filesystem service.
     *
     * Filesystem current working directory is set to path given.
     *
     * @param string $path path to initial current working directory.
     */
    public function __construct($path)
    {
        parent::__construct($path);
        $this->setCwd(new DirectoryIterator(realpath($path)));
    }

    /**
     * Creates new directory in cwd.
     *
     * This method wraps standard <code>mkdir()</code> function to object oriented access.
     *
     * @param string $name new directory name.
     * @param int $mode unix-like permissions.
     * @param bool $recursive create nested also nested folders.
     * @throws IOException when directory already exists.
     * @throws IOException when creation of new directory fails.
     * @see mkdir
     */
    public function mkdir($name, $mode = 0777, $recursive = false)
    {
        if ($this->exists($name)) {
            throw new IOException(sprintf("Directory %s already exists.", $name));
        }
        try {
            if (!mkdir($this->getCwd()->getPath() . DIRECTORY_SEPARATOR . $name, $mode, $recursive)) {
                throw new IOException("Creation of new directory failed.");
            } else {
                array_push($this->files, $name);
            }
        } catch (\Exception $e) {
            throw new IOException($e->getMessage());
        }
    }

    /**
     * Change cwd.
     *
     * Moves to another directory inside current working directory.
     * Use <code>..</code> to move to parent directory.
     *
     * @param string $name name of directory to open.
     * @throws IOException when requested directory does not exists.
     * @throws IOException when requested file is not directory.
     */
    public function chdir($name)
    {
        if (!$this->exists($name)) {
            throw new IOException(sprintf("Directory with name %s does not exists.", $name));
        }
        $fileInfo = $this->getDirectoryIteratorByName($name);
        if (!$fileInfo->isDir()) {
            throw new IOException(sprintf("Cannot open %n as it is not directory."));
        }
        $this->setCwd($fileInfo);
    }

    /**
     * Alias of chdir.
     *
     * @param string $name name of directory to open.
     * @see chdir
     */
    public function cd($name)
    {
        $this->chdir($name);
    }

    /**
     * Returns file from cwd by name.
     * @param string $fileName name of file to fetch.
     * @return File
     * @throws IOException when file is not found in cwd.
     */
    public function get($fileName)
    {
        if ($this->exists($fileName)) {
            return new File($this->getPath() . DIRECTORY_SEPARATOR . $fileName);
        } else {
            throw new IOException(sprintf("File %s does not exists.", $fileName));
        }
    }

    /**
     * Checks if given file name exists in cwd.
     * @param string $name name of file to look for.
     * @return bool
     */
    public function exists($name)
    {
        return in_array($name, $this->getFiles());
    }

    /**
     * Returns files in cwd.
     * @return string[]
     */
    public function getFiles()
    {
        if (!count($this->files)) {
            $this->fillFilesList();
        }
        return $this->files;
    }

    /**
     * Change internal cwd.
     * @param DirectoryIterator $newCwd new current working directory to set.
     */
    protected function setCwd(DirectoryIterator $newCwd)
    {
        if (!$newCwd->isDir()) {
            throw new IOException("Trying to set cwd to non directory.");
        }
        $this->cwd = $newCwd;
        $this->files = [];
    }

    /**
     * Fills list of files in cwd.
     */
    protected function fillFilesList()
    {
//        $this->files = [];
        $files = [];
        $cwd = $this->getCwd();
        $cwd->rewind();
//        foreach ($cwd as $file) {
//            if($cwd->valid()) {
//                $this->files[$cwd->key()] = $file->getFilename();
//            }
//        }
        while ($cwd->valid()) {
            $files[$cwd->key()] = $cwd->current()->getFilename();
            $cwd->next();
        }
        $this->files = $files;
    }

    /**
     * Returns iterator for sub folder by name.
     * @param string $name name of file to get iterator for.
     * @return DirectoryIterator
     * @throws IOException when file is not present in cwd.
     */
    protected function getDirectoryIteratorByName($name)
    {
        $key = array_search($name, $this->files, true);
        if ($key === false) {
            throw new IOException(sprintf("File %s does not exists.", $name));
        } else {
            return new DirectoryIterator($this->current()->getPath() . DIRECTORY_SEPARATOR . $name);
        }
    }

    /**
     * Returns information object by name from cwd.
     * @param string $name name of file to get file info for.
     * @return SplFileInfo
     * @throws IOException when file is not present in cwd.
     */
    protected function getFileInfoByName($name)
    {
        $key = array_search($name, $this->files, true);
        if ($key === false) {
            throw new IOException(sprintf("File %s does not exists.", $name));
        } else {
            $this->seek($key);
            return $this->current();
        }
    }

    /**
     * Returns current working directory
     * @return DirectoryIterator
     */
    public function getCwd()
    {
        return $this->cwd;
    }

    /* All methods below behaves like decorator: call of each is delegated to CWD */

    /**
     * @inheritdoc
     */
    public function getPath()
    {
        return $this->getCwd()->getPath();
    }

    /**
     * @inheritdoc
     */
    public function getFilename()
    {
        return $this->getCwd()->getFilename();
    }

    /**
     * @inheritdoc
     */
    public function getExtension()
    {
        return $this->getCwd()->getExtension();
    }

    /**
     * @inheritdoc
     */
    public function getBasename($suffix = null)
    {
        return $this->getCwd()->getBasename($suffix);
    }

    /**
     * @inheritdoc
     */
    public function getPathname()
    {
        return $this->getCwd()->getPathname();
    }

    /**
     * @inheritdoc
     */
    public function getPerms()
    {
        return $this->getCwd()->getPerms();
    }

    /**
     * @inheritdoc
     */
    public function getInode()
    {
        return $this->getCwd()->getInode();
    }

    /**
     * @inheritdoc
     */
    public function getSize()
    {
        return $this->getCwd()->getSize();
    }

    /**
     * @inheritdoc
     */
    public function getOwner()
    {
        return $this->getCwd()->getOwner();
    }

    /**
     * @inheritdoc
     */
    public function getGroup()
    {
        return $this->getCwd()->getGroup();
    }

    /**
     * @inheritdoc
     */
    public function getATime()
    {
        return $this->getCwd()->getATime();
    }

    /**
     * @inheritdoc
     */
    public function getMTime()
    {
        return $this->getCwd()->getMTime();
    }

    /**
     * @inheritdoc
     */
    public function getCTime()
    {
        return $this->getCwd()->getCTime();
    }

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return $this->getCwd()->getType();
    }

    /**
     * @inheritdoc
     */
    public function isWritable()
    {
        return $this->getCwd()->isWritable();
    }

    /**
     * @inheritdoc
     */
    public function isReadable()
    {
        return $this->getCwd()->isReadable();
    }

    /**
     * @inheritdoc
     */
    public function isExecutable()
    {
        return $this->getCwd()->isExecutable();
    }

    /**
     * @inheritdoc
     */
    public function isFile()
    {
        return $this->getCwd()->isFile();
    }

    /**
     * @inheritdoc
     */
    public function isDir()
    {
        return $this->getCwd()->isDir();
    }

    /**
     * @inheritdoc
     */
    public function isLink()
    {
        return $this->getCwd()->isLink();
    }

    /**
     * @inheritdoc
     */
    public function getLinkTarget()
    {
        return $this->getCwd()->getLinkTarget();
    }

    /**
     * @inheritdoc
     */
    public function getRealPath()
    {
        return $this->getCwd()->getRealPath();
    }

    /**
     * @inheritdoc
     */
    public function getFileInfo($class_name = null)
    {
        return $this->getCwd()->getFileInfo($class_name);
    }

    /**
     * @inheritdoc
     */
    public function getPathInfo($class_name = null)
    {
        return $this->getCwd()->getPathInfo($class_name);
    }

    /**
     * @inheritdoc
     */
    public function openFile($open_mode = 'r', $use_include_path = false, $context = null)
    {
        return $this->getCwd()->openFile($open_mode, $use_include_path, $context);
    }

    /**
     * @inheritdoc
     */
    public function setFileClass($class_name = null)
    {
        $this->getCwd()->setFileClass($class_name);
    }

    /**
     * @inheritdoc
     */
    public function setInfoClass($class_name = null)
    {
        $this->getCwd()->setInfoClass($class_name);
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return $this->getCwd()->__toString();
    }

    /* seekable interface */

    public function current()
    {
        return $this->getCwd()->current();
    }

    public function next()
    {
        $this->getCwd()->next();
    }

    public function key()
    {
        return $this->getCwd()->key();
    }

    public function valid()
    {
        return $this->getCwd()->valid();
    }

    public function rewind()
    {
        $this->getCwd()->rewind();
    }


    public function seek($position)
    {
        $this->getCwd()->seek($position);
    }

}