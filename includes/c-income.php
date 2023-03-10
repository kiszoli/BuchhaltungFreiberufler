<?php
require_once("dbconfig.php");
require_once("c-invoice.php");
require_once("c-settings.php");
require_once("c-address.php");

class income
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

    function GetSumPerAnno($Bilanzjahr)
    {
        $sql = "SELECT Sum(Rechnungspositionen.Nettobetrag) AS SumNettobetrag";
        $sql .= " FROM Rechnungen LEFT JOIN Rechnungspositionen ON Rechnungen.id = Rechnungspositionen.RechnungsId";
        $sql .= " WHERE Rechnungen.RechnungsDatum >= " . strtotime('01.01.' . $Bilanzjahr) . " AND Rechnungen.RechnungsDatum <= " . strtotime('31.12.' . $Bilanzjahr);
        //echo $sql;
        $query = mysqli_query($this->DBLink, $sql);

        $jahresSumme = 0.00;

        if (!$query) {
            echo mysqli_error($this->DBLink);
        } else {
            if (mysqli_num_rows($query) > 0) {
                $datarow = mysqli_fetch_array($query, MYSQLI_ASSOC);
                $jahresSumme = $datarow['SumNettobetrag'];
            }
        }

        return $jahresSumme;
    }

    function GetList($Bilanzjahr)
    {
        $myBilanzStart = (strtotime('01.01.' . $Bilanzjahr));
        $myBilanzEnd = (strtotime('31.12.' . $Bilanzjahr));
        $sql = "SELECT Rechnungen.id AS 'Rechnungen_id', Rechnungen.RechnungsNr, Rechnungen.RechnungsDatum, Rechnungen.SteuerNr, Rechnungen.AbsFirma, ";
        $sql .= "Rechnungen.AbsName, Rechnungen.AbsStrasseNr, Rechnungen.AbsPLZOrt, Rechnungen.AbsTelefon, ";
        $sql .= "Rechnungen.AbsMobil, Rechnungen.AbsInternet, Rechnungen.AbsEmail, Rechnungen.KunFirma, Rechnungen.KunName, ";
        $sql .= "Rechnungen.KunStrasseNr, Rechnungen.KunPLZOrt, Rechnungen.Ueberschrift, Rechnungen.Freitext, ";
        $sql .= "Rechnungspositionen.id AS 'Rechnungspositionen_id', Rechnungspositionen.RechnungsId, Rechnungspositionen.Menge, ";
        $sql .= "Rechnungspositionen.Einheit, Rechnungspositionen.Bezeichnung, ";
        $sql .= "Rechnungspositionen.Nettobetrag ";
        $sql .= "FROM Rechnungen LEFT JOIN Rechnungspositionen ON Rechnungen.id = Rechnungspositionen.RechnungsId ";
        $sql .= "WHERE Rechnungen.RechnungsDatum >= " . $myBilanzStart . " AND Rechnungen.RechnungsDatum <= " . $myBilanzEnd . " ";
        $sql .= "ORDER BY Rechnungen.RechnungsDatum, Rechnungen.RechnungsNr";
        //echo $sql;
        $query = mysqli_query($this->DBLink, $sql);

        $accordion = '';
        $accordionHead = '';
        $accordionItems = array();
        $rechnungsNr = "";
        $rechnungsSumme = 0;
        if (!$query) {
            echo mysqli_error($this->DBLink);
        } else {
            while ($datarow = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
                if ($rechnungsNr != $datarow['RechnungsNr']) {
                    if (strlen($rechnungsNr) > 0) {
                        $accordionHead .= '<div class="rightalign">' . number_format($rechnungsSumme, 2, ',', '.') . '</div>';
                        $accordionHead = '<button class="accordion">' . $accordionHead .  '</button>' . PHP_EOL;
                        $accordion .= $accordionHead;
                        $accordion .= '<div class ="panel">' . PHP_EOL;
                        foreach ($accordionItems as $item) {
                            $accordion .= '<div class="accordionitem">' . $item . '</div>' . PHP_EOL;
                        }
                        $accordion .= '</div>' . PHP_EOL;
                    }
                    $rechnungsSumme = 0;
                    $rechnungsNr = $datarow['RechnungsNr'];
                    $accordionItems = array();
                }
                $accordionHead = '<div class="leftalign">' . $datarow['RechnungsNr'] . '-' . date("d.m.Y", $datarow['RechnungsDatum']) . '-' . $datarow['KunFirma'] . '</div>';
                $accordionHead .= '<div class="rightalign">&nbsp;<a href="' . $_SERVER['PHP_SELF'] . '?deleteinvoice=' . $datarow['Rechnungen_id'] . '" onclick="return confirm(\'Are you sure you want to Remove?\');"><img src="images/trashbin.png"></a></div>';
                $accordionHead .= '<div class="rightalign">&nbsp;<a href="' . $_SERVER['PHP_SELF'] . '?printinvoice=' . $datarow['Rechnungen_id'] . '" target="_blanc"><img src="images/print_tn.png"></a></div>';
                $accordionHead .= '<div class="rightalign">&nbsp;<a href="' . $_SERVER['PHP_SELF'] . '?insertposition=' . $datarow['Rechnungen_id'] . '"><img src="images/plus.png"></a></div>';
                $accordionHead .= '<div class="rightalign">&nbsp;<a href="' . $_SERVER['PHP_SELF'] . '?updateinvoice=' . $datarow['Rechnungen_id'] . '"><img src="images/pencil.png"></a></div>';
                $accordionHead .= '<div class="rightalign">&nbsp;<a href="' . $_SERVER['PHP_SELF'] . '?mailinvoice=' . $datarow['Rechnungen_id'] . '"><img src="images/mail.png"></a></div>';

                if ($datarow['RechnungsId'] > 0) {
                    $myItem = '<div class="leftalign"><a href="' . $_SERVER['PHP_SELF'] . '?updateposition=' . $datarow['Rechnungspositionen_id'] . '">' . $datarow['Bezeichnung'] . '</a></div>';
                    $myItem .= '<div class="rightalign"><a href="' . $_SERVER['PHP_SELF'] . '?deleteposition=' . $datarow['Rechnungspositionen_id'] . '"><img src="images/trashbin.png" onclick="return confirm(\'Are you sure you want to Remove?\');"></a></div>';
                    $myItem .= '<div class="rightalign">' . number_format($datarow['Nettobetrag'], 2, ',', '.') . '</div>';
                    $accordionItems[$datarow['Rechnungspositionen_id']] = $myItem;
                }
                $rechnungsSumme = $rechnungsSumme + $datarow['Nettobetrag'];
            }
            //der letzte
            if (strlen($rechnungsNr) > 0) {
                $accordionHead .= '<div class="rightalign">' . number_format($rechnungsSumme, 2, ',', '.') . '</div>';
                $accordionHead = '<button class="accordion">' . $accordionHead .  '</button>' . PHP_EOL;
                $accordion .= $accordionHead;
                $accordion .= '<div class ="panel">' . PHP_EOL;
                foreach ($accordionItems as $item) {
                    $accordion .= '<div class="accordionitem">' . $item . '</div>' . PHP_EOL;
                }
                $accordion .= '</div>' . PHP_EOL;
            }
        }


        return $accordion;
    }

    private function GetRechnungsNr($Kuerzel, $Bilanzjahr)
    {
        $sql = "SELECT * FROM Rechnungen WHERE RechnungsNr LIKE '" . $Kuerzel . $Bilanzjahr . "%'";
        $query = mysqli_query($this->DBLink, $sql);
        if (!$query) {
            echo mysqli_error($this->DBLink);
            return 0;
        }
        $newNum = mysqli_num_rows($query) + 1;

        return $Kuerzel . $Bilanzjahr . str_pad($newNum, 4, "0", STR_PAD_LEFT);
    }

    function GetInvoiceForm($RechnungsId, $KundenId, $Kuerzel, $Bilanzjahr)
    {
        $myInvoice = new invoice();
        if ($RechnungsId > 0) $myInvoice->Load($RechnungsId);
        else {
            $myInvoice->RechnungsNr = $this->GetRechnungsNr($Kuerzel, $Bilanzjahr);
            $mySettings = new settings();
            if ($Kuerzel == 'G') {
                $myInvoice->Ueberschrift = $mySettings->TitelGutschrift;
                $myInvoice->Freitext = $mySettings->TextGutschrift;
            } else {
                $myInvoice->Ueberschrift = $mySettings->TitelRechnung;
                $myInvoice->Freitext = $mySettings->TextRechnung;
            }
            $myAddress = new address();
            $myAddress->Load($KundenId);
            $myInvoice->AdressenId = $myAddress->id;
            $myInvoice->KunFirma = $myAddress->Firma;
            $myInvoice->KunName = $myAddress->Ansprechpartner;
            $myInvoice->KunStrasseNr = $myAddress->StrasseNr;
            $myInvoice->KunPLZOrt = $myAddress->PLZ . ' ' . $myAddress->Ort;
            $myInvoice->KunLand = $myAddress->Land;
        }

        $myForm = '<div class="formblock">';

        $myForm .= '<form action="' . $_SERVER['PHP_SELF'] . '" method="post" enctype="application/x-www-form-urlencoded">' . PHP_EOL;

        $myForm .= '<input type="hidden" name="userdata[Rechnungen_id]" value="' . $RechnungsId . '" />' . PHP_EOL;
        $myForm .= '<input type="hidden" name="userdata[AdressenId]" value="' . $KundenId . '" />' . PHP_EOL;

        $myForm .= '<div class="listentry">' . PHP_EOL;
        $myForm .= '<div class="fourthcolumn"><input type="text" name="userdata[RechnungsNr]" placeholder="RechnungsNr" value="' . $myInvoice->RechnungsNr . '" /></div>' . PHP_EOL;
        $myForm .= '<div class="fourthcolumn"><input type="text" name="userdata[RechnungsDatum]" placeholder="Datum" value="' . $myInvoice->DateOut() . '" /></div>' . PHP_EOL;
        $myForm .= '</div>' . PHP_EOL;

        $myForm .= '<div class="listentry">' . PHP_EOL;
        $myForm .= '<div class="halfcolumn"><input type="text" name="userdata[KunFirma]" placeholder="Firma" value="' . $myInvoice->KunFirma . '" /></div>' . PHP_EOL;
        $myForm .= '</div>' . PHP_EOL;
        $myForm .= '<div class="listentry">' . PHP_EOL;
        $myForm .= '<div class="halfcolumn"><input type="text" name="userdata[KunName]" placeholder="Ansprechpartner" value="' . $myInvoice->KunName . '" /></div>' . PHP_EOL;
        $myForm .= '</div>' . PHP_EOL;
        $myForm .= '<div class="listentry">' . PHP_EOL;
        $myForm .= '<div class="halfcolumn"><input type="text" name="userdata[KunStrasseNr]" placeholder="Strasse Nr" value="' . $myInvoice->KunStrasseNr . '" /></div>' . PHP_EOL;
        $myForm .= '</div>' . PHP_EOL;
        $myForm .= '<div class="listentry">' . PHP_EOL;
        $myForm .= '<div class="halfcolumn"><input type="text" name="userdata[KunPLZOrt]" placeholder="PLZ Ort" value="' . $myInvoice->KunPLZOrt . '" /></div>' . PHP_EOL;
        $myForm .= '</div>' . PHP_EOL;
        $myForm .= '<div class="listentry">' . PHP_EOL;
        $myForm .= '<div class="halfcolumn"><input type="text" name="userdata[KunLand]" placeholder="Land" value="' . $myInvoice->KunLand . '" /></div>' . PHP_EOL;
        $myForm .= '</div>' . PHP_EOL;

        $myForm .= '<div class="listentry">' . PHP_EOL;
        $myForm .= '<div class="fullcolumn"><input type="text" name="userdata[Ueberschrift]" placeholder="Ueberschrift" value="' . $myInvoice->Ueberschrift . '" /></div>' . PHP_EOL;
        $myForm .= '</div>' . PHP_EOL;
        $myForm .= '<div class="listentry">' . PHP_EOL;
		$myForm .= '<div class="fullcolumn"><textarea rows="4" cols="60" name="userdata[Freitext]" placeholder="Text">' . $myInvoice->Freitext . '</textarea></div>' . PHP_EOL;
		$myForm .= '</div>' . PHP_EOL;

        $myForm .= '<div class="listentry">' . PHP_EOL;
		$myForm .= '<div class="fourthcolumn"><input type="submit" name="saveinvoice" value="Speichern" /></div>' . PHP_EOL;
        $myForm .= '<div class="fourthcolumn"><input type="submit" name="cancel" value="Abbrechen" /></div>' . PHP_EOL;
		$myForm .= '</div>' . PHP_EOL;

        $myForm .= '</div>';
        return $myForm;
    }

    function GetPositionForm($RechnungsId, $PositionId)
    {
        $myPosition = new position();
        if ($PositionId > 0) $myPosition->Load($PositionId);
        else $myPosition->RechnungsId = $RechnungsId;

        $myForm = '<form action="' . $_SERVER['PHP_SELF'] . '" method="post" enctype="application/x-www-form-urlencoded">' . PHP_EOL;
        $myForm .= '<table width="100%">' . PHP_EOL;
        $myForm .= '<tr>' . PHP_EOL;
        $myForm .= '<td><input type="hidden" name="userdata[Rechnungspositionen_id]" value="' . $myPosition->id . '" />' . PHP_EOL;
        $myForm .= '<input type="hidden" name="userdata[RechnungsId]" value="' . $myPosition->RechnungsId . '" />' . PHP_EOL;
        $myForm .= '<td width="65"><input type="text" name="userdata[Menge]" placeholder="Menge" value="' . $myPosition->Menge . '" /></td>' . PHP_EOL;
        $myForm .= '<td width="60"><input type="text" name="userdata[Einheit]" placeholder="Einheit" value="' . $myPosition->Einheit . '" /></td>' . PHP_EOL;
        $myForm .= '<td><input type="text" name="userdata[Bezeichnung]" placeholder="Bezeichnung" value="' . $myPosition->Bezeichnung . '" /></td>' . PHP_EOL;
        $myForm .= '<td width="70"><input type="text" name="userdata[Nettobetrag]" placeholder="Netto" value="' . $myPosition->GetNetto() . '" /></td>' . PHP_EOL;
        $myForm .= '<td width="50"><input type="submit" name="saveposition" value="Save" /></td>' . PHP_EOL;
        $myForm .= '<td width="50"><input type="submit" name="cancel" value="Cancel" /></td>' . PHP_EOL;
        $myForm .= '</tr>' . PHP_EOL;
        $myForm .= '</table>' . PHP_EOL;
        $myForm .= '</form>' . PHP_EOL;
        return $myForm;
    }

    function GetContent($ContentNo, $Bilanzjahr, $KundenId, $Kuerzel, $RechnungsId, $PositionId)
    {
        switch ($ContentNo) {
            case 1:
                return $this->GetInvoiceForm($RechnungsId, $KundenId, $Kuerzel, $Bilanzjahr);
                break;
            case 2:
                return $this->GetPositionForm($RechnungsId, $PositionId);
                break;
            default:
                return $this->GetList($Bilanzjahr);
        }
    }

    function GetExportList($Bilanzjahr)
    {
        $excelData[0] = array('RechnungsNr', 'Datum', 'Kunde', 'Rechnungsposition', 'Netto', 'MwSt', 'Brutto');

        $myBilanzStart = (strtotime('01.01.' . $Bilanzjahr));
        $myBilanzEnd = (strtotime('31.12.' . $Bilanzjahr));

        $sql = "SELECT Rechnungspositionen.id, Rechnungen.RechnungsNr, Rechnungen.RechnungsDatum, Rechnungen.KunFirma, Rechnungspositionen.Bezeichnung, Rechnungen.Steuersatz, Rechnungspositionen.Nettobetrag ";
        $sql .= "FROM Rechnungen LEFT JOIN Rechnungspositionen ON Rechnungen.id = Rechnungspositionen.RechnungsId ";
        $sql .= "WHERE Rechnungen.RechnungsDatum >= " . $myBilanzStart . " AND Rechnungen.RechnungsDatum <= " . $myBilanzEnd . " ";
        $sql .= "ORDER BY Rechnungen.RechnungsNr";

        $query = mysqli_query($this->DBLink, $sql);
        if (!$query) {
            echo mysqli_error($this->DBLink);
        } else {
            while ($datarow = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
                $mwst = $datarow['Nettobetrag'] / 100 * $datarow['Steuersatz'];
                $brutto = $datarow['Nettobetrag'] + $mwst;
                $excelData[$datarow['id']] = array(
                    $datarow['RechnungsNr'],
                    date("d.m.Y", $datarow['RechnungsDatum']), 
                    $datarow['KunFirma'], 
                    $datarow['Bezeichnung'], 
                    $datarow['Nettobetrag'],
                    $mwst,
                    $brutto);
            }
        }
        return $excelData;
    }

    function PrintInvoice($RechnungsId)
    {
        $myInvoice = new invoice();
        $myInvoice->Print($RechnungsId);
    }

    function SaveInvoice($Userdata)
    {
        $InvoiceId = $Userdata['Rechnungen_id'];

        $myInvoice = new invoice();
        return $myInvoice->Save($Userdata, $InvoiceId);
    }

    function DeleteInvoice($RechnungsId)
    {
        $myInvoice = new invoice();
        return $myInvoice->Delete($RechnungsId);
    }

    function SavePosition($Userdata)
    {
        $PositionId = $Userdata['Rechnungspositionen_id'];

        $myPosition = new position();
        $myPosition->Save($Userdata, $PositionId);
    }

    function DeletePosition($PositionId)
    {
        $myPosition = new position();
        $myPosition->Delete($PositionId);
    }

    function MailInvoice($RechnungsId){
        $myInvoice = new invoice();
        $myInvoice->MailInvoice($RechnungsId);
    }
}
