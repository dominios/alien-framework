<?php
class VariableItemView extends ContentItemView {

    const Icon = 'variable.png';
    const Name = 'Variabilná oblasť';
    const Type = 'VariableItem';

    private $limit;
    private $name;
    private $items = null;

    public function __construct($id, $row = null){
        parent::__construct($id, $row);
    }

    public function getType(){
        return self::Type;
    }

    public function getIcon(){
        return self::Icon;
    }

    public function getLimit(){
        return (int)$this->limit;
    }

    public function getName(){
        return self::Name;
    }

    public function fetchViews(ContentPage $page){

        if(!$this->getItem(true) instanceof ContentItem){
            return Array();
        } else {
            if($this->items === null){
                $arr = Array();
                $DBH = Alien::getDatabaseHandler();
                foreach($DBH->query('SELECT * FROM '.ALien::getDBPrefix().'_content_views WHERE id_c='.(int)$this->getItem(true)->getContainer().' && id_p = '.$page->getId()) as $row){
                    $item = ContentItemView::getSpecificView($row['id_v'], $row['id_type'], $row);
                    if($item instanceof ContentItemView){
                        $arr[] = $item;
                    }
                }
                $this->items = $arr;
            }
            return $this->items;
        }
    }
}