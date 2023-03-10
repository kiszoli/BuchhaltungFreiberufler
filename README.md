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

Geplant ist
- EÜR pro Monat, Quartal, Jahr

Installation
1. Alle Dateien in ein Verzeichnis kopieren (Webspace mit mindestens 100MB freiem Speicherplatz, PHP 7, MySQL)
2. Den Ordner rechnungen beschreibbar machen (770)
3. Datenbank anlegen
4. Zugangsdaten für die Datenbank in die Datei dbconfig.php eintragen
5. index.php mit dem Browser aufrufen
6. Benutzername und Passwort eingeben, merken und Login anklicken
Wichtig!!
Der Benutzername und das Passwort das beim ersten Aufruf eingegeben wird ist für spätere Anmeldungen notwendig. Wählt weise!!

3rd Party Libraries
https://github.com/tecnickcom/tc-lib-pdf

https://github.com/PHPMailer/PHPMailer

https://github.com/shuchkin/simplexlsxgen
