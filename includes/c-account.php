<?php
require_once("dbconfig.php");

class account
{
    var $id = 0;
    var $KontoNr = '';
    var $Bezeichnung = '';
    var $ProzentAbsetzbar = 100;

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
    }

    private function CreateTable()
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

    function Load($AccountId) {
        $sql = "SELECT * FROM Konten WHERE id = " . $AccountId;
        $query = mysqli_query($this->DBLink, $sql);

        if (!$query) {
            echo mysqli_error($this->DBLink);
            return 0;
        } else {
            $datarow = mysqli_fetch_array($query, MYSQLI_ASSOC);
            $this->id = $AccountId;
            $this->KontoNr = $datarow['KontoNr'];
            $this->Bezeichnung = $datarow['Bezeichnung'];
            $this->ProzentAbsetzbar = $datarow['ProzentAbsetzbar'];
        }

        return $AccountId;
    }

    private function SetData($Userdata) {
        $this->id = $Userdata['id'];
        $this->KontoNr = $Userdata['KontoNr'];
        $this->Bezeichnung = $Userdata['Bezeichnung'];
        $this->ProzentAbsetzbar = $Userdata['ProzentAbsetzbar'];
    }

    private function Insert() {
        $sql = "INSERT INTO Konten (KontoNr, Bezeichnung, Absetzbar)";
        $sql .= " VALUES ('" . $this->KontoNr . "', ";
        $sql .= "'" . $this->Bezeichnung . "', ";
        $sql .= $this->ProzentAbsetzbar . ")";
        $query = mysqli_query($this->DBLink, $sql);
        if (!$query) {
            echo mysqli_error($this->DBLink);
            return 0;
        }

        return mysqli_insert_id($this->DBLink);
    }

    private function Update($ExpenseId) {
        $sql = "UPDATE Konten SET ";
        $sql .= "KontoNr = '" . $this->KontoNr . "', ";
        $sql .= "Bezeichnung = '" . $this->Bezeichnung . "', ";
        $sql .= "Absetzbar = " . $this->ProzentAbsetzbar;
        $sql .= " WHERE id = " . $ExpenseId;
        $query = mysqli_query($this->DBLink, $sql);
        if (!$query) {
            echo mysqli_error($this->DBLink);
        }
        return $this->id;
    }

    function Save($Userdata, $AccountId = 0) {
        $this->SetData($Userdata);
        if ($AccountId > 0) return $this->Update($AccountId);
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
