<?php

namespace Application\Controllers\Rest;

use Alien\Rest\BaseRestfulController;
use Alien\Filesystem\File;

class NavController extends BaseRestfulController
{

    private function getStorageFileName()
    {
        return __DIR__ . '/../../../../storage/navigation.serialized';
    }

    protected function getFakeContent()
    {
        return [
            [
                'link' => '#link',
                'label' => 'link'
            ],
            [
                'link2' => '#link2',
                'label' => 'link2'
            ]
        ];
    }

    public function listAction()
    {
        $file = new File($this->getStorageFileName());
        $content = unserialize($file->getFileContent());
        $file->close();
        return $this->dataResponse($content);
    }

    public function patchAction()
    {
        $fileContent = $this->getRequest()->getContent();
        $json = json_decode($fileContent, true);

        $file = new File($this->getStorageFileName());
        $file->setFileContent(serialize($json));
        $file->save();
        $file->close();

        return $this->successResponse();

    }

}