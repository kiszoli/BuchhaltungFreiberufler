<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

require_once("includes/c-setup.php");
$mySetup = new setup();
$mySetup->Run();

require_once("includes/c-settings.php");

session_start();
$_SESSION['userId'] = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login'])) {
        $mySettings = new settings();
        $_SESSION['userId'] = $mySettings->Login($_POST['Benutzername'], $_POST['Passwort']);
    }
}

if ($_SESSION['userId'] > 0) header('Location: index.php');
?>
<!doctype html>
<html lang="de">

<head>
    <title>Buchhaltung</title>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="css/style.css" />
</head>

<body>
    <div id="wrapper">
        <!-- Login -->
        <h1>Buchhaltung</h1>
        <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post" enctype="application/x-www-form-urlencoded" class="loginfield">
            <label for="Benutzername">Benutzername</label>
            <input type="text" name="Benutzername" id="Benutzername" value="" />
            <label for="Passwort">Passwort</label>
            <input type="password" name="Passwort" id="Passwort" value="" />
            <input type="submit" name="login" id="login" value="Login" />
        </form>
        <!-- /Login -->
    </div>
</body>

</html>