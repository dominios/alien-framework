
<? return; ?>

<div class="item">
    <img src="<?= \Alien\Application::$SystemImgUrl . '/' . $this->Item->getIcon(); ?>"><?= $this->Item->getName(); ?>
    <div class="itemCP">
        <? if ($this->Item instanceof Models\Content\ContentItem): ?>
            <a href="<?= $this->Item->actionEdit(); ?>"><img src="<?= \Alien\Application::$SystemImgUrl; ?>/edit.png" title="Upraviť objekt"></a>
        <? endif; ?>
        <a href="<?= $this->Item->actionEdit(); ?>"><img src="<?= \Alien\Application::$SystemImgUrl; ?>/service.png" title="Prispôsobiť zobrazovač"></a>
        <a href="<?= $this->Item->actionDrop(); ?>"><img src="<?= \Alien\Application::$SystemImgUrl; ?>/delete.png" title="Odstrániť zo zobrazenia"></a>
    </div>
    <?
//    if ($this->Item instanceof VariableItemView) {
////    var_dump($this->Item->getItem(true)->getContainer() );
//        var_dump($this->Item->fetchViews(new ContentPage(1)));
//    }
    ?>
</div>