<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

session_start();
if (isset($_GET['logout'])) {
	session_gc();
	session_destroy();
	header('Location: login.php');
}
if (!isset($_SESSION['userId']) || $_SESSION['userId'] < 1) header('Location: login.php');

require_once("includes/c-account.php");

$Konten = new account();
$KontenId = 0;
if (isset($_GET['update'])) $KontenId = $_GET['update'];
if (isset($_GET['delete'])) $Konten->Delete($_GET['delete']);
if (isset($_POST['save'])) $Konten->Save($_POST['userdata']);

?>
<!doctype html>
<html lang="de">

<head>
	<title>Buchhaltung</title>
	<meta charset="utf-8" />
	<link rel="stylesheet" href="css/style.css" />
</head>

<body>
	<div id="menu">
		<div class="logo"><img src="images/logo_tn.png"></div>
		<div class="navigation">
			<a href="index.php">EÜR</a>
			<a href="rechnungen.php"> | Einnahmen</a>
			<a href="ausgaben.php"> | Ausgaben</a>
			<a href="adressen.php"> | Adressen</a>
			<a href="kontenrahmen.php"> | Kontenrahmen</a>
			<a href="einstellungen.php"> | Einstellungen</a>
			<a href="<?php echo $_SERVER['PHP_SELF']; ?>?logout=true"> | Logout</a>
		</div>
		<div class="clearfix"></div>
	</div>
	<div class="clearfix"></div>

	<div id="wrapper">
		<h1>Konten</h1>
		<?php echo $Konten->GetAccountList($KontenId); ?>
	</div>
	<div class="clearfix"></div>
</body>

</html>