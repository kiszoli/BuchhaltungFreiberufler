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

require_once("includes/c-income.php");
include("includes/simplexlsxgen/SimpleXLSXGen.php");

$RechnungsId = -1;
$KundenId = -1;
$PositionId = -1;
$Income = new income();
$ContentNo = 0;
//ContentNo - fuer die bessere lesbarkeit
//0 = Invoice List (default)
//1 = Invoice Form
//2 = Position Form
if (isset($_GET['insertinvoice'])) {
	$KundenId = $_GET['insertinvoice'];
	$RechnungsId = 0;
	$ContentNo = 1;
}
if (isset($_GET['updateinvoice'])) {
	$RechnungsId = $_GET['updateinvoice'];
	$ContentNo = 1;
}
if (isset($_GET['insertposition'])) {
	$RechnungsId = $_GET['insertposition'];
	$PositionId = 0;
	$ContentNo = 2;
}
if (isset($_GET['updateposition'])) {
	$PositionId = $_GET['updateposition'];
	$ContentNo = 2;
}

if (isset($_GET['printinvoice'])) $Income->PrintInvoice($_GET['printinvoice']);
if (isset($_POST['saveinvoice'])) $Income->SaveInvoice($_POST['userdata']);
if (isset($_GET['deleteinvoice'])) $Income->DeleteInvoice($_GET['deleteinvoice']);
if (isset($_POST['saveposition'])) $Income->SavePosition($_POST['userdata']);
if (isset($_GET['deleteposition'])) $Income->DeletePosition($_GET['deleteposition']);
$Kuerzel = 'R';
if (isset($_GET['kuerzel'])) $Kuerzel = $_GET['kuerzel'];
$myMessage = '';
if (isset($_GET['mailinvoice'])) $myMessage = $Income->MailInvoice($_GET['mailinvoice']);
if (isset($_GET['export'])) {
	$myData = $Income->GetExportList($_SESSION['von'], $_SESSION['bis']);

	$xlsx = Shuchkin\SimpleXLSXGen::fromArray($myData);
	$xlsx->downloadAs('einnahmen.xlsx'); // or $xlsx_content = (string) $xlsx or saveAs('books.xlsx')
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
		<h1>Einnahmen</h1>
		<?php echo $myMessage; ?>
		<?php echo $Income->GetContent($ContentNo, $_SESSION['von'], $_SESSION['bis'], $KundenId, $Kuerzel, $RechnungsId, $PositionId); ?>
		<hr>
		<a href="adressen.php">Rechnung / Gutschrift erstellen</a> | <a href="rechnungen.php?export">Excel Export</a>
		<script>
            var acc = document.getElementsByClassName("accordion");
            var i;

            for (i = 0; i < acc.length; i++) {
                acc[i].addEventListener("click", function() {
                    this.classList.toggle("active");
                    var panel = this.nextElementSibling;
                    if (panel.style.maxHeight) {
                        panel.style.maxHeight = null;
                    } else {
                        panel.style.maxHeight = panel.scrollHeight + "px";
                    }
                });
            }
        </script>
	</div>
	<div class="clearfix"></div>
</body>

</html>