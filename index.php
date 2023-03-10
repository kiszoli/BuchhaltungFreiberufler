<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

require_once("includes/c-income.php");
require_once("includes/c-expense.php");
include("includes/simplexlsxgen/SimpleXLSXGen.php");

session_start();
if (isset($_GET['logout'])) $_SESSION['userId'] = 0;
if (!isset($_SESSION['userId']) || $_SESSION['userId'] < 1) header('Location: login.php');

if (!isset($_SESSION['bilanzjahr'])) $_SESSION['bilanzjahr'] = date("Y");
if (isset($_GET['bilanzdelta'])) $_SESSION['bilanzjahr'] = $_SESSION['bilanzjahr'] + $_GET['bilanzdelta'];

$myEinnahmen = new income();
$myAusgaben = new expense();

if (isset($_GET['export'])) {
	$myData = $myAusgaben->GetExportListEUER($_SESSION['bilanzjahr'], $myEinnahmen->GetSumPerAnno($_SESSION['bilanzjahr']));

	$xlsx = Shuchkin\SimpleXLSXGen::fromArray($myData);
	$xlsx->downloadAs('E-Ue-R.xlsx'); // or $xlsx_content = (string) $xlsx or saveAs('books.xlsx')
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
		<h1>Einnahmen-Überschussrechnung</h1>
		<?php echo $myAusgaben->GetEUER($_SESSION['bilanzjahr'], $myEinnahmen->GetSumPerAnno($_SESSION['bilanzjahr'])); ?>
		<p><a href="index.php?export">Excel Export</a></p>
	</div>
	<div class="clearfix"></div>
</body>

</html>