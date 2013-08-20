<!DOCTYPE HTML>
<html>
<head>
    <title><?=$this->Title; ?> | ALiEN</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta name="description" content="Administračné rozhranie redakčného systému ALiEN CMS.">
    <meta name="author" content="Dominik Geršák">
    <meta name="robots" content="noindex, nofollow">
    <link rel="shortcut icon" href="images/icons/favicon.ico">
    <!--<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>-->
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <!--<script type="text/javascript" src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>-->
    <script type="text/javascript" src="js/jquery-ui.js"></script>
    <link type="text/css" href="display/ui-smoothness/jquery-ui-1.10.3.custom.css" rel="stylesheet">


    <!--
        <script type="text/javascript" src="js/ckeditor/ckeditor.js"></script>
        <script type="text/javascript" src="js/browser.js"></script>
        <script type="text/javascript" src="js/alien.js"></script>
    -->

    <script type="text/javascript" src="js/alien.js"></script>
    <script type="text/javascript" src="js/alien2.js"></script>
    <link href="display/alien.css" type="text/css" rel="stylesheet">
    <link href="display/alien2.css" type="text/css" rel="stylesheet">
</head>
<body>
    <?=$this->Notifications; ?>
    <div id="topbanner"><strong style="font-size: 20px; margin-left: 25px;"><?=$this->Webname; ?></strong></div>
    
    <div id="toppanel">
        <a href="index.php" class="nobackground" id="alienlogo"><img src="display/img/alien_logo_white.png" alt="ALiEN CMS"></a>
        <div id="toppanel_menu"><?=$this->MainMenu; ?></div>
    </div>
    
    <div id="maincontent">
        <div class="leftpanel">
            <h3><?=$this->LeftTitle; ?></h3>
            <div class="hr"></div>
            <div id="leftMenu">
                <?=$this->LeftBox; ?>
            </div>
        </div>
        <div class="rightpanel">
            <div id="rightpanelHeader">
                <div id="headerTitle"><?=$this->Title; ?></div>
                <div class="hr"></div>
                <div id="headerActionPanel"><?=$this->ActionMenu; ?></div>
            </div>
            <div style="margin-top: 10px;"><?=$this->MainContent; ?></div>
        </div>
    </div>
    <div style="clear: both;"></div>
</body>
</html>