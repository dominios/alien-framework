<?php

namespace Application\Controllers\Rest;

use Alien\Rest\BaseRestfulController;

class NavController extends BaseRestfulController
{

    private function getStorageFileName()
    {
        return __DIR__ . '/../../../../storage/navigation.serialized';
    }

    function listAction()
    {
        $data = file_get_contents($this->getStorageFileName());
        return $this->dataResponse(json_decode(unserialize($data), true));
    }

    function patchAction()
    {
        $fileContent = $this->getRequest()->getContent();
        file_put_contents($this->getStorageFileName(), serialize($fileContent));
        return $this->dataResponse([]);
    }

}