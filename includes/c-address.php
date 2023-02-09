<?php
require_once("dbconfig.php");

class address
{
    var $id = 0;
    var $Firma = "";
    var $Ansprechpartner = "";
    var $StrasseNr = "";
    var $PLZ = "";
    var $Ort = "";
    var $Land = "";
    var $Telefon = "";
    var $Mobil = "";
    var $Email = "";
    var $Homepage = "";
    var $Notiz = "";

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
    }

    function Load($AdressenId)
    {
        $sql = "SELECT * FROM Adressen WHERE id = " . $AdressenId;
        $query = mysqli_query($this->DBLink, $sql);

        if (!$query) {
            echo mysqli_error($this->DBLink);
        } else {
            $datarow = mysqli_fetch_array($query, MYSQLI_ASSOC);
            $this->id = $AdressenId;
            $this->Firma = $datarow['Firma'];
            $this->Ansprechpartner = $datarow['Ansprechpartner'];
            $this->StrasseNr = $datarow['StrasseNr'];
            $this->PLZ = $datarow['PLZ'];
            $this->Ort = $datarow['Ort'];
            $this->Land = $datarow['Land'];
            $this->Telefon = $datarow['Telefon'];
            $this->Mobil = $datarow['Mobil'];
            $this->Email = $datarow['Email'];
            $this->Homepage = $datarow['Homepage'];
            $this->Notiz = $datarow['Notiz'];
        }
    }

    private function SetData($Userdata)
    {
        $this->Firma = $Userdata['Firma'];
        if ($this->Firma == "") $this->Firma = $Userdata['Ansprechpartner'];
        $this->Ansprechpartner = $Userdata['Ansprechpartner'];
        $this->StrasseNr = $Userdata['StrasseNr'];
        $this->PLZ = $Userdata['PLZ'];
        $this->Ort = $Userdata['Ort'];
        $this->Land = $Userdata['Land'];
        $this->Telefon = $Userdata['Telefon'];
        $this->Mobil = $Userdata['Mobil'];
        $this->Email = $Userdata['Email'];
        $this->Homepage = $Userdata['Homepage'];
        $this->Notiz = $Userdata['Notiz'];
    }

    private function Insert()
    {
        $sql = "INSERT INTO Adressen (Firma, Ansprechpartner, StrasseNr, PLZ, Ort, Land, Telefon, Mobil, Email, Homepage, Notiz)";
        $sql .= " VALUES (";
        $sql .= "'" . $this->Firma . "', ";
        $sql .= "'" . $this->Ansprechpartner . "', ";
        $sql .= "'" . $this->StrasseNr . "', ";
        $sql .= "'" . $this->PLZ . "', ";
        $sql .= "'" . $this->Ort . "', ";
        $sql .= "'" . $this->Land . "', ";
        $sql .= "'" . $this->Telefon . "', ";
        $sql .= "'" . $this->Mobil . "', ";
        $sql .= "'" . $this->Email . "', ";
        $sql .= "'" . $this->Homepage . "', ";
        $sql .= "'" . $this->Notiz . "')";

        $query = mysqli_query($this->DBLink, $sql);
        if (!$query) {
            echo mysqli_error($this->DBLink);
            return 0;
        }

        return mysqli_insert_id($this->DBLink);
    }

    private function Update($AdressenId)
    {
        $sql = "UPDATE Adressen SET ";
        $sql .= "Firma = '" . $this->Firma . "', ";
        $sql .= "Ansprechpartner = '" . $this->Ansprechpartner . "', ";
        $sql .= "StrasseNr = '" . $this->StrasseNr . "', ";
        $sql .= "PLZ = '" . $this->PLZ . "', ";
        $sql .= "Ort = '" . $this->Ort . "', ";
        $sql .= "Land = '" . $this->Land . "', ";
        $sql .= "Telefon = '" . $this->Telefon . "', ";
        $sql .= "Mobil = '" . $this->Mobil . "', ";
        $sql .= "Email = '" . $this->Email . "', ";
        $sql .= "Homepage = '" . $this->Homepage . "', ";
        $sql .= "Notiz = '" . $this->Notiz . "' ";
        $sql .= "WHERE id = " . $AdressenId;
        $query = mysqli_query($this->DBLink, $sql);
        if (!$query) {
            echo mysqli_error($this->DBLink);
            return 0;
        } else $this->id = $AdressenId;
        
        return $this->id;
    }

    function Save($Userdata, $AdressenId = 0)
    {
        $this->SetData($Userdata);
        if ($AdressenId == 0) return $this->Insert();
        return $this->Update($AdressenId);
    }

    function Delete($AdressenId) {
        $sql = "DELETE FROM Adressen WHERE id = " . $AdressenId;
        $query = mysqli_query($this->DBLink, $sql);
        if (!$query) {
            echo mysqli_error($this->DBLink);
            return $AdressenId;
        }
        return -1;
    }
}
