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

require_once("includes/c-expense.php");
include("includes/simplexlsxgen/SimpleXLSXGen.php");

if (!isset($_SESSION['von'])) $_SESSION['von'] = $_SESSION['von'] = strtotime(date('Y') . '-01');
if (!isset($_SESSION['bis'])) $_SESSION['bis'] = $_SESSION['bis'] = strtotime(date('Y') . '-12');

if (isset($_POST['startDate'])) $_SESSION['von'] = strtotime($_POST['startDate']);
if (isset($_POST['endDate'])) $_SESSION['bis'] = strtotime($_POST['endDate']);

$myAusgaben = new expense();

if (isset($_GET['export'])) {
	$myData = $myAusgaben->GetExportListEUER($_SESSION['von'], $_SESSION['bis']);

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
		<form action="index.php" method="post" enctype="application/x-www-form-urlencoded" id="formId">
			<label for="startDate">Von </label>
			<input type="month" id="startDate" name="startDate" min="2000-01" value="<?php echo date('Y-m', $_SESSION['von']); ?>" onchange="this.form.submit()">
			<label for="endDate"> Bis </label>
			<input type="month" id="endDate" name="endDate" min="2000-02" value="<?php echo date('Y-m', $_SESSION['bis']); ?>" onchange="this.form.submit()">
		</form>
		<h1>Einnahmen-Überschussrechnung</h1>
		<?php echo $myAusgaben->GetEUER($_SESSION['von'], $_SESSION['bis']); ?>
		<p><a href="index.php?export">Excel Export</a></p>
	</div>
	<div class="clearfix"></div>
</body>

</html>