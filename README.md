# BuchhaltungFreiberufler
PHP/MySQL Webfrontend für Rechnungen, Ausgaben, Adressen und einfacher EÜR

Diese Anwendung ist auf meine eigenen Bedürfnisse zugeschnitten. Ich war genervt weil die Software, die ich gefunden habe zu teuer/überladen/unnötig kompliziert ist.
Funktionen wie Warenbestände, Verschiedene Steuersätze oder Ähnliches gibt es hier nicht.

Mit dieser Anwendung werden
- Rechnungen erstellt, als PDF gespeichert und gedruckt
- Ausgaben erfasst
- Kundenadressen verwaltet
- Eine einfache jährliche EÜR erstellt

Geplant ist
- Rechnungen / Gutschriften per Mail verschicken
- Export der Einnahmen / Ausgaben / EÜR als Excel-Datei
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