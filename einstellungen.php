<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

require_once("includes/c-settings.php");

session_start();
if (isset($_GET['logout'])) $_SESSION['userId'] = 0;
if (!isset($_SESSION['userId']) || $_SESSION['userId'] < 1) header('Location: login.php');

$Settings = new settings();

if (isset($_POST['save'])) $Settings->Save($_POST['userdata']);

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
		<h1>Einstellungen</h1>
		<div class="formblock">
			<form action="einstellungen.php" method="post" enctype="application/x-www-form-urlencoded">
				<div class="listentry">
					<div class="halfcolumn"><input type="text" name="userdata[Benutzername]" placeholder="Benutzername" value="" /></div>
					<div class="halfcolumn"><input type="password" name="userdata[Passwort]" placeholder="Passwort" value="" /></div>
				</div>
				<hr />
				<div class="listentry">
					<div class="fullcolumn"><input type="text" name="userdata[Firma]" placeholder="Firma" value="<?php echo $Settings->Firma; ?>" /></div>
				</div>
				<div class="listentry">
					<div class="fullcolumn"><input type="text" name="userdata[Ansprechpartner]" placeholder="Ansprechpartner" value="<?php echo $Settings->Ansprechpartner; ?>" /></div>
				</div>
				<div class="listentry">
					<div class="halfcolumn"><input type="text" name="userdata[StrasseNr]" placeholder="Strasse, Nr" value="<?php echo $Settings->StrasseNr; ?>" /></div>
				</div>
				<div class="listentry">
					<div class="fourthcolumn"><input type="text" name="userdata[PLZ]" placeholder="PLZ" value="<?php echo $Settings->PLZ; ?>" /></div>
					<div class="halfcolumn"><input type="text" name="userdata[Ort]" placeholder="Ort" value="<?php echo $Settings->Ort; ?>" /></div>
				</div>
				<div class="listentry">
					<div class="halfcolumn"><input type="text" name="userdata[Land]" placeholder="Land" value="<?php echo $Settings->Land; ?>" /></div>
				</div>
				<div class="listentry">
					<div class="halfcolumn"><input type="text" name="userdata[Telefon]" placeholder="Telefon" value="<?php echo $Settings->Telefon; ?>" /></div>
					<div class="halfcolumn"><input type="text" name="userdata[Mobil]" placeholder="Mobil" value="<?php echo $Settings->Mobil; ?>" /></div>
				</div>
				<div class="listentry">
					<div class="halfcolumn"><input type="email" name="userdata[Email]" placeholder="Email" value="<?php echo $Settings->Email; ?>" /></div>
					<div class="halfcolumn"><input type="url" name="userdata[Internet]" placeholder="Internet" value="<?php echo $Settings->Internet; ?>" /></div>
				</div>
				<div class="listentry">
					<div class="halfcolumn"><input type="text" name="userdata[SteuerNr]" placeholder="Steuer-Nr" value="<?php echo $Settings->SteuerNr; ?>" /></div>
					<div class="halfcolumn"><input type="text" name="userdata[SteuerId]" placeholder="Steuer-Id" value="<?php echo $Settings->SteuerId; ?>" /></div>
				</div>
				<div class="listentry">
					<div class="fourthcolumn"><br>Steuersatz</div>
					<div class="fourthcolumn"><input type="number" name="userdata[Steuersatz]" value="<?php echo $Settings->Steuersatz; ?>" /></div>
				</div>
				<div class="listentry">
					<div class="fullcolumn"><input type="text" name="userdata[Kontoinhaber]" placeholder="Kontoinhaber" value="<?php echo $Settings->Kontoinhaber; ?>" /></div>
				</div>
				<div class="listentry">
					<div class="fullcolumn"><input type="text" name="userdata[BankName]" placeholder="Bank" value="<?php echo $Settings->BankName; ?>" /></div>
				</div>
				<div class="listentry">
					<div class="halfcolumn"><input type="text" name="userdata[IBAN]" placeholder="IBAN" value="<?php echo $Settings->IBAN; ?>" /></div>
					<div class="halfcolumn"><input type="text" name="userdata[BIC]" placeholder="BIC" value="<?php echo $Settings->BIC; ?>" /></div>
				</div>
				<div class="listentry">
					<div class="fullcolumn"><input type="text" name="userdata[TitelRechnung]" placeholder="Titel Rechnung" value="<?php echo $Settings->TitelRechnung; ?>" /></div>
				</div>
				<div class="listentry">
					<div class="fullcolumn"><textarea rows="4" cols="60" name="userdata[TextRechnung]" placeholder="Text Rechnung"><?php echo $Settings->TextRechnung; ?></textarea></div>
				</div>
				<div class="listentry">
					<div class="fullcolumn"><input type="text" name="userdata[TitelGutschrift]" placeholder="Titel Gutschrift" value="<?php echo $Settings->TitelGutschrift; ?>" /></div>
				</div>
				<div class="listentry">
					<div class="fullcolumn"><textarea rows="4" cols="60" name="userdata[TextGutschrift]" placeholder="Text Gutschrift"><?php echo $Settings->TextGutschrift; ?></textarea></div>
				</div>
				<div class="listentry">
					<div class="fullcolumn"><input type="text" name="userdata[MailBetreff]" placeholder="Mail Betreff" value="<?php echo $Settings->MailBetreff; ?>" /></div>
				</div>
				<div class="listentry">
					<div class="fullcolumn"><textarea rows="4" cols="60" name="userdata[MailRechnung]" placeholder="Mail Text Rechnung"><?php echo $Settings->MailRechnung; ?></textarea></div>
				</div>
				<div class="listentry">
					<div class="fullcolumn"><textarea rows="4" cols="60" name="userdata[MailGutschrift]" placeholder="Mail Text Gutschrift"><?php echo $Settings->MailGutschrift; ?></textarea></div>
				</div>
				<div class="listentry">
					<div class="fourthcolumn"><input type="submit" name="save" value="Save" /></div>
				</div>
			</form>
		</div>
	</div>
	<div class="clearfix"></div>
</body>

</html>