<section id="ConsoleContainer" style="display: none;">
    <header id="ConsoleHeader"><span class="icon icon-console"></span>Console</header>
    <section id="ConsoleContent">
        <?
        foreach ($this->messages as $m) {
            echo ('<span class="ConsoleTime">[' . date('d.m.Y H:i:s', $m['time']) . ']</span> <span class="' . $m['level'] . '">' . $m['msg'] . '</span><br>');
        }
        ?>
    </section>
    <div id="ConsoleInput">admin@alien $ <input class="ConsoleInput"></div>
</section>