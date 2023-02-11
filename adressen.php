<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

require_once("includes/c-addresses.php");

session_start();
if (isset($_GET['logout'])) $_SESSION['userId'] = 0;
if (!isset($_SESSION['userId']) || $_SESSION['userId'] < 1) header('Location: login.php');

if (!isset($_SESSION['bilanzjahr'])) $_SESSION['bilanzjahr'] = date("Y");
if (isset($_GET['bilanzdelta'])) $_SESSION['bilanzjahr'] = $_SESSION['bilanzjahr'] + $_GET['bilanzdelta'];

$AddressId = -1;
if (isset($_GET['updateaddress'])) $AddressId = $_GET['updateaddress'];
if (isset($_POST['AddressId'])) $AddressId = $_POST['AddressId'];

$Adressen = new addresses();
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
		<div class="bilanzjahr">
			<?php echo '<a href="' . $_SERVER['PHP_SELF'] . '?bilanzdelta=-1"><img src="images/arrow-left.png"></a>' . $_SESSION['bilanzjahr'] . '<a href="' . $_SERVER['PHP_SELF'] . '?bilanzdelta=+1"><img src="images/arrow-right.png"></a>'; ?>
		</div>
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
		else echo $Adressen->GetAddressList();
		?>
	</div>
	<div class="clearfix"></div>
</body>

</html>