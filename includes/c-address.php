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

    function GetAddressList()
    {
        $sql = "SELECT Adressen.id, Adressen.Firma, Adressen.Ansprechpartner, Sum(Rechnungspositionen.Nettobetrag) AS Umsatz";
        $sql .= " FROM (Adressen LEFT JOIN Rechnungen ON Adressen.id = Rechnungen.AdressenId) LEFT JOIN Rechnungspositionen ON Rechnungen.id = Rechnungspositionen.RechnungsId";
        $sql .= " GROUP BY Adressen.id, Adressen.Firma, Adressen.Ansprechpartner ORDER BY Adressen.Firma";
        $query = mysqli_query($this->DBLink, $sql);

        $myTable = '<div class="smallcontent">';
        if (!$query) {
            echo mysqli_error($this->DBLink);
        } else {
            $myTable .= '<table>' . PHP_EOL;
            $myTable .= '<thead>' . PHP_EOL;
            $myTable .= '<th style="width:380px">Firma / Kunde</th>' . PHP_EOL;
            $myTable .= '<th>Umsatz</th>' . PHP_EOL;
            $myTable .= '<th>&nbsp;</th>' . PHP_EOL;
            $myTable .= '</thead>' . PHP_EOL;
            $myTable .= '<tbody>' . PHP_EOL;
            while ($datarow = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
                $myTable .= '<tr>' . PHP_EOL;
                $myTable .= '<td><input type="hidden" name ="userdata[id]" value="' . $datarow['id'] . '"><a href="' . $_SERVER['PHP_SELF'] . '?updateaddress=' . $datarow['id'] . '">' . $datarow['Firma'];
                if (strlen($datarow['Ansprechpartner']) > 1) $myTable .= ' - ' . $datarow['Ansprechpartner'];
                $myTable .= '</a></td>' . PHP_EOL;
                $myTable .= '<td class="tright">' . number_format($datarow['Umsatz'], 2, ',', '.') . '</td>';
                $myTable .= '<td class="tright"><a href="rechnungen.php?insertinvoice=' . $datarow['id'] . '">Rechnung</a>' . PHP_EOL;
                $myTable .= '<a href="rechnungen.php?insertinvoice=' . $datarow['id'] . '&kuerzel=G">Gutschrift</a></td>' . PHP_EOL;
                $myTable .= '</tr>' . PHP_EOL;
            }
            $myTable .= '</tbody>' . PHP_EOL;
            $myTable .= '</table>' . PHP_EOL;
        }
        $myTable .= '</div>';
        return $myTable;
    }

    function GetAddressForm($AddressId)
    {
        if ($AddressId > 0) $this->Load($AddressId);
        $myForm = '<div class="formblock">' . PHP_EOL;

        $myForm .= '<form action="' . $_SERVER['PHP_SELF'] . '" method="post" enctype="application/x-www-form-urlencoded">' . PHP_EOL;

        $myForm .= '<input type="hidden" name="AddressId" value="' . $this->id . '" />' . PHP_EOL;

        $myForm .= '<div class="listentry">' . PHP_EOL;
        $myForm .= '<div class="fullcolumn"><input type="text" name="userdata[Firma]" placeholder="Firma" value="' . $this->Firma . '" /></div>' . PHP_EOL;
        $myForm .= '</div>' . PHP_EOL;

        $myForm .= '<div class="listentry">' . PHP_EOL;
        $myForm .= '<div class="fullcolumn"><input type="text" name="userdata[Ansprechpartner]" placeholder="Ansprechpartner" value="' . $this->Ansprechpartner . '" /></div>' . PHP_EOL;
        $myForm .= '</div>' . PHP_EOL;

        $myForm .= '<div class="listentry">' . PHP_EOL;
        $myForm .= '<div class="halfcolumn"><input type="text" name="userdata[StrasseNr]" placeholder="Strasse, Nr" value="' . $this->StrasseNr . '" /></div>' . PHP_EOL;
        $myForm .= '</div>' . PHP_EOL;

        $myForm .= '<div class="listentry">' . PHP_EOL;
        $myForm .= '<div class="fourthcolumn"><input type="text" name="userdata[PLZ]" placeholder="PLZ" value="' . $this->PLZ . '" /></div>' . PHP_EOL;
        $myForm .= '<div class="halfcolumn"><input type="text" name="userdata[Ort]" placeholder="Ort" value="' . $this->Ort . '" /></div>' . PHP_EOL;
        $myForm .= '</div>' . PHP_EOL;

        $myForm .= '<div class="listentry">' . PHP_EOL;
        $myForm .= '<div class="halfcolumn"><input type="text" name="userdata[Land]" placeholder="Land" value="' . $this->Land . '" /></div>' . PHP_EOL;
        $myForm .= '</div>' . PHP_EOL;

        $myForm .= '<div class="listentry">' . PHP_EOL;
        $myForm .= '<div class="halfcolumn"><input type="text" name="userdata[Telefon]" placeholder="Telefon" value="' . $this->Telefon . '" /></div>' . PHP_EOL;
        $myForm .= '<div class="halfcolumn"><input type="text" name="userdata[Mobil]" placeholder="Mobil" value="' . $this->Mobil . '" /></div>' . PHP_EOL;
        $myForm .= '</div>' . PHP_EOL;

        $myForm .= '<div class="listentry">' . PHP_EOL;
        $myForm .= '<div class="halfcolumn"><input type="email" name="userdata[Email]" placeholder="Email" value="' . $this->Email . '" /></div>' . PHP_EOL;
        $myForm .= '<div class="halfcolumn"><input type="text" name="userdata[Homepage]" placeholder="Homepage" value="' . $this->Homepage . '" /></div>' . PHP_EOL;
        $myForm .= '</div>' . PHP_EOL;

        $myForm .= '<div class="listentry">' . PHP_EOL;
        $myForm .= '<div class="fullcolumn"><textarea rows="4" cols="60" name="userdata[Notiz]" placeholder="Notiz">' . $this->Notiz . '</textarea></div>' . PHP_EOL;
        $myForm .= '</div>' . PHP_EOL;

        $myForm .= '<div class="listentry">' . PHP_EOL;
        $myForm .= '<div class="fourthcolumn"><input type="submit" name="save" value="Save" /></div>' . PHP_EOL;
        if ($AddressId > 0) {
            $myForm .= '<div class="fourthcolumn"><input type="submit" name="delete" value="Delete" onclick="return confirm(\'Wirklich Löschen?\')"/></div>' . PHP_EOL;
        }
        $myForm .= '<div class="fourthcolumn"><input type="submit" name="cancel" value="Abbrechen" /></div>' . PHP_EOL;
        $myForm .= '</div>' . PHP_EOL;

        $myForm .= '</form>' . PHP_EOL;

        $myForm .= '</div>' . PHP_EOL;

        return $myForm;
    }
}
