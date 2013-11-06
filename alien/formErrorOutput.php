<?

session_start();
if (@$_GET['request'] === 'read') {
    echo $_SESSION['formErrorOutput'];
    unset($_SESSION['formErrorOutput']);
}
die;