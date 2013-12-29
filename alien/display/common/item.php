<div class="item <?= implode(' ', $this->class); ?>" <?= $this->onClick != '' ? 'onClick="' . $this->onClick . '"' : '' ?>>
    <span class="icon icon-<?= $this->icon; ?>"></span>
    <div class="itemText"><?= $this->item->getName(); ?></div>
    <div class="itemCP">
        <? if ($this->dropLink != ''): ?>
            <a class="button" href="<?= $this->dropLink; ?>"><span class="icon icon-delete"></span></a>
            <? endif; ?>
    </div>
</div>