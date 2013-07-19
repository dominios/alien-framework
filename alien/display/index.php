<!DOCTYPE HTML>
<html>
<head>
    <title><?=$this->Title; ?> | ALiEN</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta name="description" content="Administračné rozhranie redakčného systému ALiEN CMS.">
    <meta name="author" content="Dominik Geršák">
    <meta name="robots" content="noindex, nofollow">
    <link rel="shortcut icon" href="images/icons/favicon.ico">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>  
    
        <link type="text/css" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet">
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
        <!--<script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.js"></script>-->
        <!--<script type="text/javascript" src="js/jquery-ui-1.8.21.custom.min"></script>-->
        <script type="text/javascript" src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
        <script type="text/javascript" src="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css"></script>
    
    <link type="text/css" href="display/ui-darkness/jquery-ui-1.8.21.custom.css" rel="stylesheet">    

    <script type="text/javascript" src="js/alien.js"></script>
    <script type="text/javascript" src="js/alien2.js"></script>
    
<!--        <script type="text/javascript" src="js/jquery-ui-1.8.21.custom.min.js"></script>
        <script type="text/javascript" src="js/ckeditor/ckeditor.js"></script>
        <script type="text/javascript" src="js/browser.js"></script>
        <script type="text/javascript" src="js/alien.js"></script>    -->

    
    <link href="display/alien.css" type="text/css" rel="stylesheet">
    <link href="display/alien2.css" type="text/css" rel="stylesheet">
</head>
<body>
    <?=$this->CP; ?>
    <div id="topbanner"><strong style="font-size: 20px; margin-left: 25px;"><?=$this->Webname; ?></strong></div>
    
    <div id="toppanel">
        <a href="index.php" class="nobackground" id="alienlogo"><img src="display/img/alien_logo_black.png" alt="ALiEN CMS"></a>
        <div id="toppanel_menu"><?=$this->MainMenu; ?></div>
    </div>
    
    <div id="maincontent" style="width: 1100px;">
        <div class="leftpanel"><?=$this->LeftBox; ?></div>
        <div class="rightpanel">
            <div id="rightpanelHeader">
                <div id="headerTitle"><?=$this->Title; ?></div>
                <div id="headerActionPanel"><?=$this->ActionMenu; ?></div>
            </div>
            <div style="margin-top: 10px;"><?=$this->MainContent; /* var_dump($_GET, $_POST, $_SESSION); */ ?></div>
        </div>
    </div>
    <div style="clear: both;"></div>
    
    <script type="text/javascript">// showNotifications(); </script>
</body>
</html>