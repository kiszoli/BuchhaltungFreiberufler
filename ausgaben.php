<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

require_once("includes/c-expenses.php");

session_start();
if (isset($_GET['logout'])) $_SESSION['userId'] = 0;
if (!isset($_SESSION['userId']) || $_SESSION['userId'] < 1) header('Location: login.php');

if (!isset($_SESSION['bilanzjahr'])) $_SESSION['bilanzjahr'] = date("Y");
if (isset($_GET['bilanzdelta'])) $_SESSION['bilanzjahr'] = $_SESSION['bilanzjahr'] + $_GET['bilanzdelta'];

//$oldAusgaben = new ausgaben();
$Ausgaben = new expenses();
$updateId = 0;
if (isset($_GET['update'])) $updateId = $_GET['update'];
if (isset($_GET['delete'])) $Ausgaben->Delete($_GET['delete']);
if (isset($_POST['save'])) $Ausgaben->Save($_POST['userdata']);

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
			<a href="index.php"><img src="images/home.png" alt="Home"></a>
			<a href="rechnungen.php"><img src="images/einnahmen.png" alt="Rechnungen"></a>
			<a href="ausgaben.php"><img src="images/ausgaben.png" alt="Ausgaben"></a>
			<a href="adressen.php"><img src="images/addressbook.png" alt="Adressbuch"></a>
			<a href="einstellungen.php"><img src="images/settings.png" alt="Einstellungen"></a>
			<a href="<?php echo $_SERVER['PHP_SELF']; ?>?logout=true"><img src="images/logout.png" alt="Logout"></a>
		</div>
		<div class="clearfix"></div>
	</div>
	<div class="clearfix"></div>
	<div id="wrapper">
		<h1>Ausgaben</h1>
		<?php echo $Ausgaben->GetExpensesList($_SESSION['bilanzjahr'], $updateId); ?>
	</div>
	<div class="clearfix"></div>
</body>

</html>