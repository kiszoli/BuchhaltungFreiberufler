<?php
require_once("dbconfig.php");

class dbtables{
    var $DBLink;

    function __construct()
    {
        $this->DBLink = mysqli_connect(MYSQL_HOST, MYSQL_BENUTZER, MYSQL_KENNWORT);
        if (!$this->DBLink) {
            die('Server is busy, please try again later');
        } else {
            mysqli_set_charset($this->DBLink, 'utf8');
            mysqli_select_db($this->DBLink, MYSQL_DATENBANK);
        }
        //ToDo
        //if database is empty create tables
        $this->CreateAdressen();
        $this->CreateAusgaben();
        $this->CreateEinstellungen();
        $this->CreateKonten();
    }

    private function CreateAdressen()
    {
        $sql = "CREATE TABLE IF NOT EXISTS Adressen (
            id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            Firma VARCHAR(100) NOT NULL DEFAULT '',
            Ansprechpartner VARCHAR(100) NOT NULL DEFAULT '',
            StrasseNr VARCHAR(100) NOT NULL DEFAULT '',
            PLZ VARCHAR(10) NOT NULL DEFAULT '',
            Ort VARCHAR(100) NOT NULL DEFAULT '',
            Land VARCHAR(100) NOT NULL DEFAULT '',
            Telefon VARCHAR(100) NOT NULL DEFAULT '',
            Mobil VARCHAR(100) NOT NULL DEFAULT '',
            Email VARCHAR(100) NOT NULL DEFAULT '',
            Homepage VARCHAR(100) NOT NULL DEFAULT '',
            Notiz TEXT NOT NULL DEFAULT ''
            ) DEFAULT CHARSET=utf8";
        $query = mysqli_query($this->DBLink, $sql);
        if (!$query) {
            echo mysqli_error($this->DBLink);
            return false;
        }

        return true;
    }

    private function CreateAusgaben()
    {
        $sql = "CREATE TABLE IF NOT EXISTS Ausgaben (
            id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            Datum int(17) NOT NULL DEFAULT 0,
            Bezeichnung VARCHAR(100) NOT NULL DEFAULT '',
            Brutto DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            MwSt INT(11) NOT NULL DEFAULT 0,
            KontoId INT(11) NOT NULL DEFAULT 0
            ) DEFAULT CHARSET=utf8";
        $query = mysqli_query($this->DBLink, $sql);
        if (!$query) {
            echo mysqli_error($this->DBLink);
            return false;
        }

        return true;
    }

    private function CreateEinstellungen()
    {
        $sql = "CREATE TABLE IF NOT EXISTS Einstellungen (
            id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            Benutzername VARCHAR(100) NOT NULL DEFAULT '',
            Passwort VARCHAR(100) NOT NULL DEFAULT '',
            Firma VARCHAR(100) NOT NULL DEFAULT '',
            Ansprechpartner VARCHAR(100) NOT NULL DEFAULT '',
            StrasseNr VARCHAR(100) NOT NULL DEFAULT '',
            PLZ VARCHAR(10) NOT NULL DEFAULT '',
            Ort VARCHAR(100) NOT NULL DEFAULT '',
            Land VARCHAR(100) NOT NULL DEFAULT '',
            Telefon VARCHAR(100) NOT NULL DEFAULT '',
            Mobil VARCHAR(100) NOT NULL DEFAULT '',
            Email VARCHAR(100) NOT NULL DEFAULT '',
            Internet VARCHAR(100) NOT NULL DEFAULT '',
            SteuerNr VARCHAR(100) NOT NULL DEFAULT '',
            SteuerId VARCHAR(100) NOT NULL DEFAULT '',
            Kontoinhaber VARCHAR(100) NOT NULL DEFAULT '',
            BankName VARCHAR(100) NOT NULL DEFAULT '',
            IBAN VARCHAR(100) NOT NULL DEFAULT '',
            BIC VARCHAR(100) NOT NULL DEFAULT '',
            TitelRechnung VARCHAR(100) NOT NULL DEFAULT '',
            TitelGutschrift VARCHAR(100) NOT NULL DEFAULT '',
            TextRechnung TEXT NOT NULL DEFAULT '',
            TextGutschrift TEXT NOT NULL DEFAULT '',
            MailBetreff VARCHAR(100) NOT NULL DEFAULT '',
            MailRechnung TEXT NOT NULL DEFAULT '',
            MailGutschrift TEXT NOT NULL DEFAULT '',
            Steuersatz INT(11) NOT NULL DEFAULT 0
            ) DEFAULT CHARSET=utf8";
        $query = mysqli_query($this->DBLink, $sql);
        if (!$query) {
            echo mysqli_error($this->DBLink);
            return false;
        }

        return true;
    }

    private function CreateKonten()
    {
        $sql = "CREATE TABLE IF NOT EXISTS Konten (
            id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            KontoNr VARCHAR(100) NOT NULL DEFAULT '',
            Bezeichnung VARCHAR(100) NOT NULL DEFAULT '',
            ProzentAbsetzbar INT(11) NOT NULL DEFAULT 100
            ) DEFAULT CHARSET=utf8";
        $query = mysqli_query($this->DBLink, $sql);
        if (!$query) {
            echo mysqli_error($this->DBLink);
            return false;
        }

        return true;
    }

}