<!DOCTYPE HTML>
<html style="width: 100%; height: 100%;">
<head>
  <title>ALiEN | Prihlásenie</title>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <meta name="description" content="Prihlásenie sa do redakčného systému ALiEN CMS.">
  <meta name="author" content="Dominik Geršák">
  <meta name="robots" content="noindex, nofollow">
  <link rel="shortcut icon" href="images/icons/favicon.ico">
  <link href="display/alien.css" type="text/css" rel="stylesheet">
</head>
<body style="background-color: #1f2a1f; -moz-user-select: none; -webkit-user-select: none;">
    <div style="display: block; width: 430px; height: 300px; margin: 0 auto; margin-top: 150px;">
        <img src="display/img/alien_logo_black.png" style="margin-bottom: 20px;" alt="">
        <form method="POST" action="index.php?alien=login">
            <input type="hidden" name="alien" value="login">
            <fieldset style="color: #444; border-color: #eee;  border-radius: 2px; box-shadow: 0 0 10px #ddd;">
                <legend>Prihlásenie</legend>
                <table class="noborders" style="border-spacing: 5px; border: 0px; background: none; color: #444;">
                    <tr><td><img src="display/img/user.png" alt="">&nbsp;Login:</td><td><input type="text" name="login" size="30" style="width: 300px;"></td></tr>
                    <tr><td><img src="display/img/key.png" alt="">&nbsp;Heslo:</td><td><input type="password" name="pass" size="30" style="width: 300px;"></td></tr>
                    <tr><td colspan="2" style="text-align: right;"><input type="submit" name="loginFormSubmit" value="Prihlásiť" style="padding: 2px 10px; cursor: pointer; border-radius: 2px;"></td></tr>
                </table>
            </fieldset>
        </form>
    </div>
</body>