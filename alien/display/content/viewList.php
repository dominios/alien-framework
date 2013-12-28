<?
if (!count($this->items)) {
    echo \Alien\Notification::inline('Nenašli sa žiadne objekty daného typu.', \Alien\Notification::INFO);
    return;
}
?>
<div class = "gridLayout">
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

<div class="cleaner"></div>