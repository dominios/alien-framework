<?
if (!count($this->items)) {
    echo \Alien\Notification::inline('Nenašli sa žiadne objekty daného typu.', \Alien\Notification::INFO);
    return;
}
?>

<? if ($this->layout === 'grid' || $this->layout == null): ?>

    <div class="gridLayout">
        <?
        foreach ($this->items as $item):
            $params = array(
                'item' => $item,
                'icon' => $item->getIcon(),
                'onClick' => strlen($item->actionGoTo()) ? 'window: location=\'' . $item->actionGoTo() . '\'' : '',
                'dropLink' => $item->actionDrop()
            );
            echo($this->partial('display/common/item.php', $params));
        endforeach;
        ?>
    </div>

<? elseif ($this->layout === 'row'): ?>

    <div class="rowLayout">
        <?
        foreach ($this->items as $item):
            $params = array(
                'class' => $this->sortable === true ? array('ui-state-default') : array(),
                'item' => $item,
                'icon' => $item->getIcon(),
//                'onClick' => strlen($item->actionGoTo()) ? 'window: location=\'' . $item->actionGoTo() . '\'' : '',
                'dropLink' => $item->actionDrop()
            );
            echo($this->partial('display/common/item.php', $params));
        endforeach;
        ?>
    </div>

<? endif; ?>



<div class="cleaner"></div>