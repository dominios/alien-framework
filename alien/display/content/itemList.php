<?
$actionEdit = '?content=editView&id='.$this->Item->getId();
$actionDrop = '?content=dropView&id='.$this->Item->getId();
?>

<div class="item">
    <img src="<?=ALien::$SystemImgUrl.'/'.$this->Item->getIcon();?>"><?=$this->Item->getName();?>
    <div class="itemCP">
        <a href="<?=$actionEdit;?>"><img src="<?=Alien::$SystemImgUrl;?>/edit.png"></a>
        <a href="<?=$actionDrop;?>"><img src="<?=Alien::$SystemImgUrl;?>/delete.png"></a>
    </div>
    <? if($this->Item instanceof VariableItemView){
//    var_dump($this->Item->getItem(true)->getContainer() );
        var_dump($this->Item->fetchViews(new ContentPage(1)));
    } ?>
</div>