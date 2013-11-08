<!DOCTYPE HTML>
<html>
    <head>
        <title><?= $this->Title; ?> | ALiEN</title>
        <meta http-equiv="content-type" content="text/html; charset=utf-8">
        <meta name="description" content="Administračné rozhranie redakčného systému ALiEN CMS.">
        <meta name="author" content="Dominik Geršák">
        <meta name="robots" content="noindex, nofollow">
        <link rel="shortcut icon" href="/alien/images/icons/favicon.ico">
        <script type="text/javascript" src="/alien/js/jquery-1.8.0.min.js"></script>
        <script type="text/javascript" src="/alien/js/jquery-ui.js"></script>
        <link type="text/css" href="/alien/display/alien-theme/jquery-ui-1.10.3.custom.css" rel="stylesheet">
        <script type="text/javascript" src="/alien/js/alien.js"></script>
        <script type="text/javascript" src="/alien/js/alien2.js"></script>
        <link href="/alien/display/alien.css" type="text/css" rel="stylesheet">
        <link href="/alien/display/alien2.css" type="text/css" rel="stylesheet">
        <link href="/alien/display/icons.css" type="text/css" rel="stylesheet">
        <link href="/alien/display/icons-data.css" type="text/css" rel="stylesheet">
    </head>
    <body>

        <div id="alienBackground"></div>

        <section id="notifications">
            <?= $this->Notifications; ?>
        </section>

        <section id="topbanner">
            <strong style="font-size: 20px; margin-left: 25px;"><?= $this->Webname; ?></strong>
        </section>

        <section id="toppanel">
            <a href="index.php" class="nobackground" id="alienlogo"><img src="/alien/display/img/alien_logo_white.png" alt="ALiEN CMS"></a>
            <div id="toppanel_menu"><?= $this->MainMenu; ?></div>
        </section>

        <section id="maincontent">
            <section class="leftpanel">
                <h3><?= $this->LeftTitle; ?></h3>
                <div class="hr" style="margin: 5px 0;"></div>
                <div id="leftMenu">
                    <?= $this->LeftBox; ?>
                </div>
            </section>
            <section class="rightpanel">
                <div id="rightpanelHeader">
                    <div id="headerTitle"><?= $this->Title; ?></div>
                    <div class="hr" style="margin: 5px 0;"></div>
                    <div id="headerActionPanel"><?= $this->ActionMenu; ?></div>
                </div>
                <div style="margin-top: 20px;"><?= $this->MainContent; ?></div>
            </section>
        </section>

        <div style="clear: both;"></div>

    </body>
</html>