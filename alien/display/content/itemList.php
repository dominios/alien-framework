<?

namespace Alien;
?>
<div class="item">
    <img src="<?= Alien::$SystemImgUrl . '/' . $this->Item->getIcon(); ?>"><?= $this->Item->getName(); ?>
    <div class="itemCP">
        <a href="<?= $this->Item->actionEdit(); ?>"><img src="<?= Alien::$SystemImgUrl; ?>/edit.png"></a>
        <a href="<?= $this->Item->actionDrop(); ?>"><img src="<?= Alien::$SystemImgUrl; ?>/delete.png"></a>
    </div>
    <?
//    if ($this->Item instanceof VariableItemView) {
////    var_dump($this->Item->getItem(true)->getContainer() );
//        var_dump($this->Item->fetchViews(new ContentPage(1)));
//    }
    ?>
</div>