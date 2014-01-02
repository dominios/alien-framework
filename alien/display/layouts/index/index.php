<!DOCTYPE HTML>
<html>
    <head>
        <title><?= $this->title; ?> | ALiEN</title>
        <meta http-equiv="content-type" content="text/html; charset=utf-8">
        <meta name="description" content="Administračné rozhranie redakčného systému ALiEN CMS.">
        <meta name="author" content="Dominik Geršák">
        <meta name="robots" content="noindex, nofollow">
        <link rel="shortcut icon" href="/alien/display/img/favicon.ico">
        <script type="text/javascript" src="/alien/js/jquery-1.8.0.min.js"></script>
        <script type="text/javascript" src="/alien/js/jquery-ui.js"></script>
        <link type="text/css" href="/alien/display/alien-theme/jquery-ui-1.10.3.custom.css" rel="stylesheet">
        <script type="text/javascript" src="/alien/js/alien.js"></script>
        <script type="text/javascript" src="/alien/js/alien2.js"></script>
        <script type="text/javascript" src="/alien/js/tabs.js"></script>
        <link href="/alien/display/layouts/index/alien.css" type="text/css" rel="stylesheet">
        <link href="/alien/display/layouts/index/alien2.css" type="text/css" rel="stylesheet">
        <link href="/alien/display/icons.css" type="text/css" rel="stylesheet">
        <link href="/alien/display/badges.css" type="text/css" rel="stylesheet">
        <link href="/alien/display/forms.css" type="text/css" rel="stylesheet">
        <link href="/alien/display/alerts.css" type="text/css" rel="stylesheet">
        <link href="/alien/display/navbar.css" type="text/css" rel="stylesheet">
        <link href="/alien/display/sidebar.css" type="text/css" rel="stylesheet">
        <link href="/alien/display/tabs.css" type="text/css" rel="stylesheet">
        <link href="/alien/display/icons-data.css" type="text/css" rel="stylesheet">
        <link href="/alien/display/modal.css" type="text/css" rel="stylesheet">
        <!-- posledne, mozu byt overridy -->
        <link href="/alien/display/layouts/index/layout.css" type="text/css" rel="stylesheet">
    </head>
    <body>

        <div id="alienBackground"></div>

        <section id="notifications">
            <?= $this->notifications; ?>
        </section>

        <nav id="toppanel" class="navbar navbar-full">
            <header class="navbar-header">
                <img src="/alien/display/img/alien_logo_white.png" alt="ALiEN">
            </header>
            <?= $this->mainMenu; ?>
        </nav>

        <div id="container"<?= $_SESSION['temp_layoutFullsize'] == 'true' ? '' : ' class="layout-fullsize"'; ?>>

            <section id="mainPanel" class="column">
                <div id="rightpanelHeader">
                    <h1><?= $this->title; ?></h1>
                    <div class="hr"></div>
                    <!--<div id="headerActionPanel"><?= $this->actionMenu; ?></div>-->
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