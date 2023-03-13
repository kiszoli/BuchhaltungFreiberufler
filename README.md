# BuchhaltungFreiberufler
PHP/MySQL Webfrontend für Rechnungen, Ausgaben, Adressen und einfacher EÜR ohne Vorsteuer

Funktionen
- Rechnungen erstellen
- PDF Erzeugen
- per Mail versenden
- Ausgaben erfassen
- Kundenadressen verwalten
- Eine einfache jährliche EÜR erstellen
- Export der Einnahmen / Ausgaben / EÜR als Excel-Datei
- EÜR monatlich von - bis

Installation
1. Alle Dateien in ein Verzeichnis kopieren (Webspace mit mindestens 100MB freiem Speicherplatz, PHP 7, MySQL)
2. Den Ordner /rechnungen erstellen und beschreibbar machen (770)
3. Datenbank anlegen
4. /includes/dbconfig-sample.php in /includes/dbconfig.php umbenennen und Zugangsdaten für die Datenbank eintragen
5. index.php mit dem Browser aufrufen
6. Benutzername und Passwort eingeben, merken und Login anklicken
7. Die Dateien /images/logo.png und /images/logo_tn.png mit dem eigenen Logo ersetzen
Wichtig!!
Der Benutzername und das Passwort das beim ersten Aufruf eingegeben wird ist für spätere Anmeldungen notwendig. Wählt weise!!

3rd Party Libraries
- PDF https://github.com/tecnickcom/tc-lib-pdf
- PHPMailer https://github.com/PHPMailer/PHPMailer
- Excel Export https://github.com/shuchkin/simplexlsxgen

Es wird keine Haftung für die Richtigkeit der Berechnungen oder sonstiger Fehler übernommen. Diese Anwendung ist nur eine Hilfestellung und ersetzt in keinem Fall den Steuerberater.