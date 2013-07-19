<section id="ConsoleContainer">
    <header id="ConsoleHeader"><img src="display/img/console.png">Console</header>
    <section id="ConsoleContent">
        <?
        foreach($this->Messages as $m){
            echo ('<span class="ConsoleTime">['.date('d.m.Y H:i:s', $m['time']).']</span> <span class="'.$m['level'].'">'.$m['msg'].'</span><br>');
        }
        ?>
    </section>
    <div id="ConsoleInput">[admin@alien] $ <input class="ConsoleInput"></div>
</section>