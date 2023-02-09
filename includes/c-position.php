<?php
class position {
    var $id = 0;
    var $RechnungsId = 0;
    var $Menge = 1;
    var $Einheit = '';
    var $Pauschal = 0;
    var $Bezeichnung = '';
    var $Nettobetrag = 0.00;

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
        $sql = "CREATE TABLE IF NOT EXISTS Rechnungspositionen (
            id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            RechnungsId int(11) NOT NULL DEFAULT 0,
            Menge int(11) NOT NULL DEFAULT 1,
            Einheit VARCHAR(100) NOT NULL DEFAULT '',
            Bezeichnung VARCHAR(100) NOT NULL DEFAULT '',
            Nettobetrag DECIMAL(10,2) NOT NULL DEFAULT 0.00
            ) DEFAULT CHARSET=utf8";
        $query = mysqli_query($this->DBLink, $sql);
        if (!$query) {
            echo mysqli_error($this->DBLink);
            return false;
        }

        return true;
    }

    function Load($PositionId){
        $sql = "SELECT * FROM Rechnungspositionen WHERE id = " . $PositionId;
        $query = mysqli_query($this->DBLink, $sql);

        if (!$query) {
            echo mysqli_error($this->DBLink);
            return 0;
        } else {
            $datarow = mysqli_fetch_array($query, MYSQLI_ASSOC);
            $this->id = $PositionId;
            $this->RechnungsId = $datarow['RechnungsId'];
            $this->Menge = $datarow['Menge'];
            $this->Einheit = $datarow['Einheit'];
            $this->Bezeichnung = $datarow['Bezeichnung'];
            $this->Nettobetrag = $datarow['Nettobetrag'];
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

    function GetNetto() {
        return number_format($this->Nettobetrag, 2, ',', '.');
    }

    private function IntIn($Menge) {
        $res = preg_replace("/[^0-9]/", "", $Menge);
        if ($res == '') return 0;
        else return $res;
    }

    private function SetData($Userdata) {
        $this->RechnungsId = $Userdata['RechnungsId'];
        $this->Menge = $this->IntIn($Userdata['Menge']);
        $this->Einheit = $Userdata['Einheit'];
        $this->Bezeichnung = $Userdata['Bezeichnung'];
        $this->Nettobetrag = $this->CurrencyIn($Userdata['Nettobetrag']);
    }

    private function Insert() {
        $sql = "INSERT INTO Rechnungspositionen (RechnungsId, Menge, Einheit, Bezeichnung, Nettobetrag)";
        $sql .= " VALUES (";
        $sql .= $this->RechnungsId . ", ";
        $sql .= $this->Menge . ", ";
        $sql .= "'" . $this->Einheit . "', ";
        $sql .= "'" . $this->Bezeichnung . "', ";
        $sql .= $this->Nettobetrag . ")";

        $query = mysqli_query($this->DBLink, $sql);
        if (!$query) {
            echo mysqli_error($this->DBLink);
            return 0;
        }

        return mysqli_insert_id($this->DBLink);
    }

    private function Update($PositionId) {
        $sql = "UPDATE Rechnungspositionen SET ";
        $sql .= "RechnungsId = " . $this->RechnungsId . ", ";
        $sql .= "Menge = " . $this->Menge . ", ";
        $sql .= "Einheit = '" . $this->Einheit . "', ";
        $sql .= "Bezeichnung = '" . $this->Bezeichnung . "', ";
        $sql .= "Nettobetrag = " . $this->Nettobetrag . " ";
        $sql .= "WHERE id = " . $PositionId;
        $query = mysqli_query($this->DBLink, $sql);
        if (!$query) {
            echo mysqli_error($this->DBLink);
            return 0;
        } else $this->id = $PositionId;
        
        return $this->id;
    }

    function Save($Userdata, $PositionId) {
        $this->SetData($Userdata);
        if ($PositionId > 0) $this->Update($PositionId);
        else $this->Insert();
    }
}