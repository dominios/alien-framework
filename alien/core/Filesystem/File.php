<?php

namespace Alien\Filesystem;

use Alien\Exception\IOException;
use SplFileObject;

class File extends \SplFileInfo implements FileInterface
{

    /**
     * @var SplFileObject
     */
    private $fileObject;

    /**
     * @var string
     */
    private $fileContent;

    public function __destruct()
    {
        $this->close();
    }

    /**
     * Returns file object for current file.
     * @return SplFileObject
     */
    protected function getFileObject()
    {
        if($this->fileObject === null) {
            $this->fileObject = $this->openFile('r+');
        }
        return $this->fileObject;
    }

    /**
     * Returns whole file content as string.
     * @return string
     */
    public function getFileContent()
    {
        if (is_null($this->fileContent)) {
            $this->fileContent = $this->getSize() === 0 ? "" : $this->getFileObject()->fread($this->getSize());
        }
        return $this->fileContent;
    }

    /**
     * Sets content.
     *
     * <b>WARNING:</b> Any existing content will be lost!
     * @param string $fileContent
     */
    public function setFileContent($fileContent)
    {
        $this->fileContent = $fileContent;
    }

    /**
     * Writes current file content on disk.
     * @return int number of bytes written
     * @throws IOException when file is not writable.
     * @throws IOException writing fails.
     */
    public function save()
    {
        if(!$this->isWritable()) {
            throw new IOException(sprintf("File %s is not writable.", $this->getBasename()));
        }
        $bytesWritten = $this->getFileObject()->fwrite($this->fileContent);
        if($bytesWritten === null || (strlen($this->fileContent) > 0 && $bytesWritten === 0)) {
            throw new IOException(sprintf("Could not write to file %s.", $this->getBasename()));
        }
        return $bytesWritten;
    }

    /**
     * Closes current file object.
     */
    public function close()
    {
        $this->fileObject = null;
    }

}