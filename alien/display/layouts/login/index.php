<!DOCTYPE HTML>
<html style="width: 100%; height: 100%;">
<head>
    <title>ALiEN | Prihlásenie</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta name="description" content="Prihlásenie sa do redakčného systému ALiEN CMS.">
    <meta name="author" content="Dominik Geršák">
    <meta name="robots" content="noindex, nofollow">
    <link rel="shortcut icon" href="images/icons/favicon.ico">
    <!--<link href="display/alien.css" type="text/css" rel="stylesheet">-->
    <link href="display/layouts/login/login.css" type="text/css" rel="stylesheet">
</head>
<body>
<section id="container">
    <img src="display/img/alien_logo_white.png" style="margin-bottom: 20px;" alt="ALiEN framework">

    <form method="POST" action="index.php">
        <input type="hidden" name="action" value="alien/login">
        <table>
            <tr>
                <td><input type="text" name="login" placeholder="Login"></td>
            </tr>
            <tr>
                <td><input type="password" name="pass" placeholder="Password"></td>
            </tr>
            <tr>
                <td><input type="submit" name="loginFormSubmit" value="Sign in"</td>
            </tr>
        </table>
    </form>
</section>

<footer>
    ALiEN CMS | © 2013 - <?= date('Y'); ?> <a href="mailto: domgersak@gmail.com" target="_blank">Dominik Geršák</a>,
    všetky práva vyhradené | optmimalizované pre 1366x768+ 32bit
</footer>
</body>