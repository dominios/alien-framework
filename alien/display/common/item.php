<div class="item" <?= $this->onClick != '' ? 'onClick="' . $this->onClick . '"' : '' ?>>
    <span class="icon icon-<?= $this->icon; ?>"></span>
    <div class="itemText"><?= $this->item->getName(); ?></div>
    <div class="itemCP">
        <? if ($this->dropLink != ''): ?>
            <a class="button" href="<?= $this->dropLink; ?>"><span class="icon icon-delete"></span></a>
            <? endif; ?>
    </div>

    <?
//    if ($this->Item instanceof VariableItemView) {
////    var_dump($this->Item->getItem(true)->getContainer() );
//        var_dump($this->Item->fetchViews(new ContentPage(1)));
//    }
    ?>
</div>