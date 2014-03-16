<!DOCTYPE HTML>
<html>
    <head>
        <title><?= $this->title; ?> | ALiEN</title>
        <meta http-equiv="content-type" content="text/html; charset=utf-8">
        <meta name="description" content="Administračné rozhranie redakčného systému ALiEN CMS.">
        <meta name="author" content="Dominik Geršák">
        <meta name="robots" content="noindex, nofollow">
        <link rel="shortcut icon" href="/alien/display/img/favicon.ico">
        <?=$this->metaScripts; ?>
        <?=$this->metaStylesheets; ?>
    </head>
    <body>

        <div id="alienBackground"></div>

        <div class="modal modal-overlay"></div>

        <section id="notifications">
            <?= $this->notifications; ?>
        </section>

        <nav id="toppanel" class="navbar navbar-full">
            <header class="navbar-header">
                <img src="/alien/display/img/alien_logo_white.png" alt="ALiEN">
            </header>
            <?= $this->mainMenu; ?>
        </nav>

        <div id="container"<?= $_COOKIE['layoutFullsize'] == 'true' ? '' : ' class="layout-fullsize"'; ?>>

            <section id="mainPanel" class="column">
                <div id="rightpanelHeader">
                    <h1><?= $this->title; ?></h1>
                    <div class="hr"></div>
                </div>
                <div style="margin-top: 35px;"><?= $this->mainContent; ?></div>
            </section>

            <aside id="leftPanel" class="column sidebar">
                <div class="inner">
                    <header>
                        <div class="hr"></div>
                        <h1><span class="icon icon-left-round" id="mainmenuMinimizer"></span><?= $this->leftTitle; ?></h1>
                        <div class="hr"></div>
                    </header>
                    <nav>
                        <?= $this->leftBox; ?>
                    </nav>
                </div>
            </aside>

            <aside id="rightFloatPanel" class="sidebar sidebar-small sidebar-left disabled sidebar-draggable">
                <div class="inner">
                    <?= $this->floatPanel; ?>
                </div>
            </aside>

            <div class="cleaner"></div>

        </div>

        <div class="cleaner"></div>

        <?= $this->terminal; ?>

    </body>
</html>