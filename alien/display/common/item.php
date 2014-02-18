<div class="item <?= implode(' ', $this->class); ?>" <?= $this->onClick != '' ? 'onClick="' . $this->onClick . '"' : '' ?>>
    <span class="icon icon-<?= $this->icon; ?>"></span>
    <div class="itemText"><?= $this->item->getName(); ?></div>
    <div class="itemCP">
        <? if ($this->editLink != ''): ?>
            <a class="button" href="<?= $this->editLink; ?>"><span class="icon icon-edit"></span></a>
        <? endif; ?>
        <? if ($this->dropLink != ''): ?>
            <a class="button" href="<?= $this->dropLink; ?>"><span class="icon icon-delete"></span></a>
        <? endif; ?>
    </div>
</div>