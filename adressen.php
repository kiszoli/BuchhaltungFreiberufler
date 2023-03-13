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

require_once("includes/c-address.php");

$AddressId = -1;
if (isset($_GET['updateaddress'])) $AddressId = $_GET['updateaddress'];
if (isset($_POST['AddressId'])) $AddressId = $_POST['AddressId'];

$Adressen = new address();
if (isset($_POST['cancel'])) $AddressId = -1;
if (isset($_POST['delete'])) {
	$AddressId = $Adressen->Delete($AddressId);
}
if (isset($_POST['save'])) {
	$AddressId = $Adressen->Save($_POST['userdata'], $AddressId);
	//close form
	$AddressId = -1;
}

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
		<h1>Kunden</h1>
		<?php
		if ($AddressId >= 0) echo $Adressen->GetAddressForm($AddressId);
		else {
			echo $Adressen->GetAddressList();
		?>
		<div class="smallcontent">
			<hr>
			<a href="adressen.php?updateaddress=0">Kunden anlegen</a>
		</div>
		<?php } ?>
	</div>
	<div class="clearfix"></div>
</body>

</html>