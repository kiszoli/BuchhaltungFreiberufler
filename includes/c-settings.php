<?php
require_once("dbconfig.php");

class settings
{
    var $Firma = "";
    var $Ansprechpartner = "";
    var $StrasseNr = "";
    var $PLZ = "";
    var $Ort = "";
    var $Land = "";

    var $Telefon = "";
    var $Mobil = "";
    var $Email = "";
    var $Internet = "";

    var $SteuerNr = "";
    var $SteuerId = "";

    var $Kontoinhaber = "";
    var $BankName = "";
    var $IBAN = "";
    var $BIC = "";

    var $TitelRechnung = "";
    var $TitelGutschrift = "";

    var $TextRechnung = "";
    var $TextGutschrift = "";

    var $MailBetreff = "";
    var $MailRechnung = "";
    var $MailGutschrift = "";

    var $Steuersatz = 0;

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

        $sql = "SELECT * FROM Einstellungen";
        $query = mysqli_query($this->DBLink, $sql);

        $firstRun = false;
        if (!$query) {
            echo mysqli_error($this->DBLink);
        } else {
            if (mysqli_num_rows($query) > 0) {
                $datarow = mysqli_fetch_array($query, MYSQLI_ASSOC);
                $this->Firma = $datarow['Firma'];
                $this->Ansprechpartner = $datarow['Ansprechpartner'];
                $this->StrasseNr = $datarow['StrasseNr'];
                $this->PLZ = $datarow['PLZ'];
                $this->Ort = $datarow['Ort'];
                $this->Land = $datarow['Land'];

                $this->Telefon = $datarow['Telefon'];
                $this->Mobil = $datarow['Mobil'];
                $this->Email = $datarow['Email'];
                $this->Internet = $datarow['Internet'];

                $this->SteuerNr = $datarow['SteuerNr'];
                $this->SteuerId = $datarow['SteuerId'];

                $this->Kontoinhaber = $datarow['Kontoinhaber'];
                $this->BankName = $datarow['BankName'];
                $this->IBAN = $datarow['IBAN'];
                $this->BIC = $datarow['BIC'];

                $this->TitelRechnung = $datarow['TitelRechnung'];
                $this->TitelGutschrift = $datarow['TitelGutschrift'];

                $this->TextRechnung = $datarow['TextRechnung'];
                $this->TextGutschrift = $datarow['TextGutschrift'];

                $this->MailBetreff = $datarow['MailBetreff'];
                $this->MailRechnung = $datarow['MailRechnung'];
                $this->MailGutschrift = $datarow['MailGutschrift'];

                $this->Steuersatz = $datarow['Steuersatz'];
            } else {
                $firstRun = true;
            }
        }

        if ($firstRun) {
            $sql = "INSERT INTO Einstellungen () VALUES ()";
            $query = mysqli_query($this->DBLink, $sql);
            if (!$query) {
                echo mysqli_error($this->DBLink);
            }
        }
    }

    private function SetData($Userdata)
    {
        $this->Firma = $Userdata['Firma'];
        $this->Ansprechpartner = $Userdata['Ansprechpartner'];
        $this->StrasseNr = $Userdata['StrasseNr'];
        $this->PLZ = $Userdata['PLZ'];
        $this->Ort = $Userdata['Ort'];
        $this->Land = $Userdata['Land'];

        $this->Telefon = $Userdata['Telefon'];
        $this->Mobil = $Userdata['Mobil'];
        $this->Email = $Userdata['Email'];
        $this->Internet = $Userdata['Internet'];

        $this->SteuerNr = $Userdata['SteuerNr'];
        $this->SteuerId = $Userdata['SteuerId'];

        $this->Kontoinhaber = $Userdata['Kontoinhaber'];
        $this->BankName = $Userdata['BankName'];
        $this->IBAN = $Userdata['IBAN'];
        $this->BIC = $Userdata['BIC'];

        $this->TitelRechnung = $Userdata['TitelRechnung'];
        $this->TitelGutschrift = $Userdata['TitelGutschrift'];

        $this->TextRechnung = $Userdata['TextRechnung'];
        $this->TextGutschrift = $Userdata['TextGutschrift'];

        $this->MailBetreff = $Userdata['MailBetreff'];
        $this->MailRechnung = $Userdata['MailRechnung'];
        $this->MailGutschrift = $Userdata['MailGutschrift'];

        $this->Steuersatz = $Userdata['Steuersatz'];
    }

    function Save($Userdata)
    {
        $this->SetData($Userdata);
        $sql = "UPDATE Einstellungen SET ";
        if ($Userdata['Benutzername'] != "") $sql .= "Benutzername = '" . $Userdata['Benutzername'] . "', ";
        if ($Userdata['Passwort'] != "") $sql .= "Passwort = '" . md5($Userdata['Passwort']) . "', ";

        $sql .= "Firma = '" . $this->Firma . "', ";
        $sql .= "Ansprechpartner = '" . $this->Ansprechpartner . "', ";
        $sql .= "StrasseNr = '" . $this->StrasseNr . "', ";
        $sql .= "PLZ = '" . $this->PLZ . "', ";
        $sql .= "Ort = '" . $this->Ort . "', ";
        $sql .= "Land = '" . $this->Land . "', ";

        $sql .= "Telefon = '" . $this->Telefon . "', ";
        $sql .= "Mobil = '" . $this->Mobil . "', ";
        $sql .= "Email = '" . $this->Email . "', ";
        $sql .= "Internet = '" . $this->Internet . "', ";

        $sql .= "SteuerNr = '" . $this->SteuerNr . "', ";
        $sql .= "SteuerId = '" . $this->SteuerId . "', ";

        $sql .= "Kontoinhaber = '" . $this->Kontoinhaber . "', ";
        $sql .= "BankName = '" . $this->BankName . "', ";
        $sql .= "IBAN = '" . $this->IBAN . "', ";
        $sql .= "BIC = '" . $this->BIC . "', ";

        $sql .= "TitelRechnung = '" . $this->TitelRechnung . "', ";
        $sql .= "TitelGutschrift = '" . $this->TitelGutschrift . "', ";

        $sql .= "TextRechnung = '" . $this->TextRechnung . "', ";
        $sql .= "TextGutschrift = '" . $this->TextGutschrift . "', ";

        $sql .= "MailBetreff = '" . $this->MailBetreff . "', ";
        $sql .= "MailRechnung = '" . $this->MailRechnung . "', ";
        $sql .= "MailGutschrift = '" . $this->MailGutschrift . "', ";

        $sql .= "Steuersatz = '" . $this->Steuersatz . "'";

        $query = mysqli_query($this->DBLink, $sql);
        if (!$query) {
            echo mysqli_error($this->DBLink);
            return false;
        }
        return true;
    }

    function Login($username, $password)
    {
        $sql = "SELECT id, Benutzername, Passwort FROM Einstellungen";
        $query = mysqli_query($this->DBLink, $sql);

        $myId = 0;
        $NewUser = false;
        if (!$query) {
            echo mysqli_error($this->DBLink);
        } else {
            $datarow = mysqli_fetch_array($query, MYSQLI_ASSOC);
            if ($username == $datarow['Benutzername'] && md5($password) == $datarow['Passwort']) return $datarow['id'];
            if ($datarow['Benutzername'] == '' && $datarow['Passwort'] == '') $NewUser = true;
        }

        if ($NewUser && strlen($username) > 2 && strlen($password) > 2) {
            $sql = "UPDATE Einstellungen SET ";
            $sql .= "Benutzername = '" . $username . "', ";
            $sql .= "Passwort = '" . md5($password) . "'";
            $query = mysqli_query($this->DBLink, $sql);
            if (!$query) {
                echo mysqli_error($this->DBLink);
            } else {
                return 1;
            }
        }
        return 0;
    }
}
