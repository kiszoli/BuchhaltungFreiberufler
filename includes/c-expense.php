<?php
require_once("dbconfig.php");

class expense
{
    var $id = 0;
    var $Datum = 0;
    var $Bezeichnung = '';
    var $Brutto = 0.00;
    var $MwSt = 19;
    var $KontoId = 0;

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

        $this->CreateTable();

        $this->Datum = time();
    }

    private function CreateTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS Einstellungen (
            id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            Datum INT(17) NOT NULL DEFAULT 0,
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

    function Load($ExpenseId) {
        $sql = "SELECT * FROM Ausgaben WHERE id = " . $ExpenseId;
        $query = mysqli_query($this->DBLink, $sql);

        if (!$query) {
            echo mysqli_error($this->DBLink);
        } else {
            $datarow = mysqli_fetch_array($query, MYSQLI_ASSOC);
            $this->id = $ExpenseId;
            $this->Datum = $datarow['Datum'];
            $this->Bezeichnung = $datarow['Bezeichnung'];
            $this->Brutto = $datarow['Brutto'];
            $this->MwSt = $datarow['MwSt'];
            $this->KontoId = $datarow['KontoId'];
        }
    }

    private function CurrencyIn($CurrencyString) {
        $res = preg_replace("/[^0-9,-]/", "", $CurrencyString);
        if (substr($res, -1) == '-') $res = rtrim($res, '-');
        $count = 0;
        $res = preg_replace('/,+/', '.', $res, -1, $count);
        if ($count == 0) $res .= '.00';
        if (substr($res, -1) == '.') $res .= '00';
        if (substr($res, 0, 1) == '.') $res = '0' . $res;
    
        return number_format($res, 2);
    }

    private function SetData($Userdata) {
        $this->id = $Userdata['id'];
        $this->Datum = strtotime($Userdata['Datum']);
        $this->Bezeichnung = $Userdata['Bezeichnung'];
        $this->Brutto = $this->CurrencyIn($Userdata['Brutto']);
        $this->MwSt = $Userdata['MwSt'];
        $this->KontoId = $Userdata['KontoId'];
    }

    private function Insert() {
        $sql = "INSERT INTO Ausgaben (Datum, Bezeichnung, Brutto, MwSt, KontoId)";
        $sql .= " VALUES (" . $this->Datum . ", ";
        $sql .= "'" . $this->Bezeichnung . "', ";
        $sql .= $this->Brutto . ", ";
        $sql .= $this->MwSt . ", ";
        $sql .= $this->KontoId . ")";
        $query = mysqli_query($this->DBLink, $sql);
        if (!$query) {
            echo mysqli_error($this->DBLink);
            return 0;
        }

        $this->id = mysqli_insert_id($this->DBLink);

        return $this->id;
    }

    private function Update($ExpenseId) {
        $sql = "UPDATE Ausgaben SET ";
        $sql .= "Datum = " . $this->Datum . ", ";
        $sql .= "Bezeichnung = '" . $this->Bezeichnung . "', ";
        $sql .= "Brutto = " . $this->Brutto . ", ";
        $sql .= "MwSt = " . $this->MwSt . ", ";
        $sql .= "KontoId = " . $this->KontoId . " ";
        $sql .= "WHERE id = " . $ExpenseId;
        $query = mysqli_query($this->DBLink, $sql);
        if (!$query) {
            echo mysqli_error($this->DBLink);
        }
        return $this->id;
    }

    function Save($Userdata, $ExpenseId = 0) {
        $this->SetData($Userdata);
        if ($ExpenseId > 0) return $this->Update($ExpenseId);
        else return $this->Insert();
    }

    function Delete($ExpenseId) {
        $sql = "DELETE FROM Ausgaben WHERE id = " . $ExpenseId;
        $query = mysqli_query($this->DBLink, $sql);
        if (!$query) {
            echo mysqli_error($this->DBLink);
            return $ExpenseId;
        }
        return -1;
    }
}
