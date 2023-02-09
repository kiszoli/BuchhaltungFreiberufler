<?php
require_once("dbconfig.php");
require_once("c-address.php");

class addresses
{
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

    function GetAddressList()
    {
        $sql = "SELECT Adressen.id, Adressen.Firma, Adressen.Ansprechpartner, Sum(Rechnungspositionen.Nettobetrag) AS Umsatz";
        $sql .= " FROM (Adressen LEFT JOIN Rechnungen ON Adressen.id = Rechnungen.AdressenId) LEFT JOIN Rechnungspositionen ON Rechnungen.id = Rechnungspositionen.RechnungsId";
        $sql .= " GROUP BY Adressen.id, Adressen.Firma, Adressen.Ansprechpartner ORDER BY Adressen.Firma";
        $query = mysqli_query($this->DBLink, $sql);

        $myTable = '';
        if (!$query) {
            echo mysqli_error($this->DBLink);
        } else {
            $myTable .= '<table>' . PHP_EOL;
            while ($datarow = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
                $myTable .= '<tr>' . PHP_EOL;
                $myTable .= '<td><input type="hidden" name ="userdata[id]" value="' . $datarow['id'] . '"></td>' . PHP_EOL;
                $myTable .= '<td><a href="' . $_SERVER['PHP_SELF'] . '?updateaddress=' . $datarow['id'] . '">' . $datarow['Firma'];
                if (strlen($datarow['Ansprechpartner']) > 1) $myTable .= ' - ' . $datarow['Ansprechpartner'];
                $myTable .= '</a></td>' . PHP_EOL;
                $myTable .= '<td class="tright">' . number_format($datarow['Umsatz'], 2, ',', '.') . '</td>';
                $myTable .= '<td class="tright"><a href="rechnungen.php?insertinvoice=' . $datarow['id'] . '">Rechnung</a>' . PHP_EOL;
                $myTable .= '<a href="rechnungen.php?insertinvoice=' . $datarow['id'] . '&kuerzel=G">Gutschrift</a></td>' . PHP_EOL;
                $myTable .= '</tr>' . PHP_EOL;
            }
            $myTable .= '</table>' . PHP_EOL;
            $myTable .= '<p><a href="adressen.php?updateaddress=0">Kunden anlegen</a></p>';
        }
        return $myTable;
    }

    function GetAddressForm($AddressId)
    {
        $myAddress = new address();
        if ($AddressId > 0) $myAddress->Load($AddressId);
        $myForm = '<div class="formblock">' . PHP_EOL;

        $myForm .= '<form action="' . $_SERVER['PHP_SELF'] . '" method="post" enctype="application/x-www-form-urlencoded">' . PHP_EOL;

        $myForm .= '<input type="hidden" name="AddressId" value="' . $myAddress->id . '" />' . PHP_EOL;

        $myForm .= '<div class="listentry">' . PHP_EOL;
        $myForm .= '<div class="fullcolumn"><input type="text" name="userdata[Firma]" placeholder="Firma" value="' . $myAddress->Firma . '" /></div>' . PHP_EOL;
        $myForm .= '</div>' . PHP_EOL;

        $myForm .= '<div class="listentry">' . PHP_EOL;
        $myForm .= '<div class="fullcolumn"><input type="text" name="userdata[Ansprechpartner]" placeholder="Ansprechpartner" value="' . $myAddress->Ansprechpartner . '" /></div>' . PHP_EOL;
        $myForm .= '</div>' . PHP_EOL;

        $myForm .= '<div class="listentry">' . PHP_EOL;
        $myForm .= '<div class="halfcolumn"><input type="text" name="userdata[StrasseNr]" placeholder="Strasse, Nr" value="' . $myAddress->StrasseNr . '" /></div>' . PHP_EOL;
        $myForm .= '</div>' . PHP_EOL;

        $myForm .= '<div class="listentry">' . PHP_EOL;
        $myForm .= '<div class="fourthcolumn"><input type="text" name="userdata[PLZ]" placeholder="PLZ" value="' . $myAddress->PLZ . '" /></div>' . PHP_EOL;
        $myForm .= '<div class="halfcolumn"><input type="text" name="userdata[Ort]" placeholder="Ort" value="' . $myAddress->Ort . '" /></div>' . PHP_EOL;
        $myForm .= '</div>' . PHP_EOL;

        $myForm .= '<div class="listentry">' . PHP_EOL;
        $myForm .= '<div class="halfcolumn"><input type="text" name="userdata[Land]" placeholder="Land" value="' . $myAddress->Land . '" /></div>' . PHP_EOL;
        $myForm .= '</div>' . PHP_EOL;

        $myForm .= '<div class="listentry">' . PHP_EOL;
        $myForm .= '<div class="halfcolumn"><input type="text" name="userdata[Telefon]" placeholder="Telefon" value="' . $myAddress->Telefon . '" /></div>' . PHP_EOL;
        $myForm .= '<div class="halfcolumn"><input type="text" name="userdata[Mobil]" placeholder="Mobil" value="' . $myAddress->Mobil . '" /></div>' . PHP_EOL;
        $myForm .= '</div>' . PHP_EOL;

        $myForm .= '<div class="listentry">' . PHP_EOL;
        $myForm .= '<div class="halfcolumn"><input type="email" name="userdata[Email]" placeholder="Email" value="' . $myAddress->Email . '" /></div>' . PHP_EOL;
        $myForm .= '<div class="halfcolumn"><input type="url" name="userdata[Homepage]" placeholder="Homepage" value="' . $myAddress->Homepage . '" /></div>' . PHP_EOL;
        $myForm .= '</div>' . PHP_EOL;

        $myForm .= '<div class="listentry">' . PHP_EOL;
        $myForm .= '<div class="fullcolumn"><textarea rows="4" cols="60" name="userdata[Notiz]" placeholder="Notiz">' . $myAddress->Notiz . '</textarea></div>' . PHP_EOL;
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

    function Delete($AddressId)
    {
        $myAddress = new address();
        return $myAddress->Delete($AddressId);
    }

    function Save($Userdata, $AddressId)
    {
        $myAddress = new address();
        return $myAddress->Save($Userdata, $AddressId);
    }
}
