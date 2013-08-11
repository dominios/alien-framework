<?php

class AlienAdminLayout extends AlienLayout {

    const SRC = 'display/index.php';
    const useConsole = true;
    const useNotifications = true;

    private $Title = '';
    private $MainMenu = '';
    private $ContentLeft = '';
    private $ContentMain = '';

    public function getPartials(){
        return Array(
            'Title' => $this->Title,
            'MainMenu' => $this->MainMenu,
            'LeftBox' => $this->ContentLeft,
            'MainContent' => $this->ContentMain
        );
    }

    public function handleResponse(ActionResponse $response){
        $data = $response->getData();
        if(isset($data['Title'])){
            $this->Title = $data['Title'];
        }
        if(isset($data['ContentLeft'])){
            $this->ContentLeft = $data['ContentLeft'];
        }
        if(isset($data['ContentMain'])){
            $this->ContentMain .= $data['ContentMain'];
        }
        if(isset($data['MainMenu'])){
            $this->MainMenu = $data['MainMenu'];
        }
    }

}