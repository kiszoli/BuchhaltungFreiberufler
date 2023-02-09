<?php
require_once("dbconfig.php");
require_once("c-settings.php");
require_once("c-position.php");
require_once('tcpdf/tcpdf_include.php');

class invoice
{
    var $id = 0;
    var $AdressenId = 0;

    var $RechnungsNr = '';
    var $RechnungsDatum = 0;
    var $SteuerNr = '';
    var $SteuerID = '';
    var $Steuersatz = 0;

    var $AbsFirma = '';
    var $AbsName = '';
    var $AbsStrasseNr = '';
    var $AbsPLZOrt = '';
    var $AbsTelefon = '';
    var $AbsMobil = '';
    var $AbsInternet = '';
    var $AbsEmail = '';

    var $KunFirma = '';
    var $KunName = '';
    var $KunStrasseNr = '';
    var $KunPLZOrt = '';
    var $KunLand = '';

    var $Ueberschrift = '';
    var $Freitext = '';

    var $Kontoinhaber = '';
    var $BankName = '';
    var $IBAN = '';
    var $BIC = '';

    var $Positionen = array();

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

        $mySettings = new settings();
        $this->RechnungsDatum = time();
        $this->SteuerNr = $mySettings->SteuerNr;
        $this->SteuerID = $mySettings->SteuerId;
        $this->Steuersatz = $mySettings->Steuersatz;

        $this->AbsFirma = $mySettings->Firma;
        $this->AbsName = $mySettings->Ansprechpartner;
        $this->AbsStrasseNr = $mySettings->StrasseNr;
        $this->AbsPLZOrt = $mySettings->PLZ . ' ' . $mySettings->Ort;
        $this->AbsTelefon = $mySettings->Telefon;
        $this->AbsMobil = $mySettings->Mobil;
        $this->AbsInternet = $mySettings->Internet;
        $this->AbsEmail = $mySettings->Email;

        $this->Kontoinhaber = $mySettings->Kontoinhaber;
        $this->BankName = $mySettings->BankName;
        $this->IBAN = $mySettings->IBAN;
        $this->BIC = $mySettings->BIC;

        $this->CreateTable();
    }

    private function CreateTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS Rechnungen (
            id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            AdressenId int(11) NOT NULL DEFAULT 0
            RechnungsNr VARCHAR(10) NOT NULL DEFAULT '',
            RechnungsDatum INT(17) NOT NULL DEFAULT 0,
            SteuerNr VARCHAR(100) NOT NULL DEFAULT '',
            SteuerID VARCHAR(100) NOT NULL DEFAULT '',
            Steuersatz INT(11) NOT NULL DEFAULT 0,
            AbsFirma VARCHAR(100) NOT NULL DEFAULT '',
            AbsName VARCHAR(100) NOT NULL DEFAULT '',
            AbsStrasseNr VARCHAR(100) NOT NULL DEFAULT '',
            AbsPLZOrt VARCHAR(100) NOT NULL DEFAULT '',
            AbsTelefon VARCHAR(100) NOT NULL DEFAULT '',
            AbsMobil VARCHAR(100) NOT NULL DEFAULT '',
            AbsInternet VARCHAR(100) NOT NULL DEFAULT '',
            AbsEmail VARCHAR(100) NOT NULL DEFAULT '',
            KunFirma VARCHAR(100) NOT NULL DEFAULT '',
            KunName VARCHAR(100) NOT NULL DEFAULT '',
            KunStrasseNr VARCHAR(100) NOT NULL DEFAULT '',
            KunPLZOrt VARCHAR(100) NOT NULL DEFAULT '',
            KunLand VARCHAR(100) NOT NULL DEFAULT '',
            Ueberschrift VARCHAR(100) NOT NULL DEFAULT '',
            Freitext TEXT NOT NULL DEFAULT '',
            Kontoinhaber VARCHAR(100) NOT NULL DEFAULT '',
            BankName VARCHAR(100) NOT NULL DEFAULT '',
            IBAN VARCHAR(100) NOT NULL DEFAULT '',
            BIC VARCHAR(100) NOT NULL DEFAULT ''
            ) DEFAULT CHARSET=utf8";
        $query = mysqli_query($this->DBLink, $sql);
        if (!$query) {
            echo mysqli_error($this->DBLink);
            return false;
        }

        return true;
    }

    function Load($RechnungsId)
    {
        $sql = "SELECT * FROM Rechnungen WHERE id = " . $RechnungsId;
        $query = mysqli_query($this->DBLink, $sql);

        if (!$query) {
            echo mysqli_error($this->DBLink);
        } else {
            if (mysqli_num_rows($query) > 0) {
                $datarow = mysqli_fetch_array($query, MYSQLI_ASSOC);
                $this->id = $RechnungsId;
                $this->AdressenId = $datarow['AdressenId'];
                $this->RechnungsNr = $datarow['RechnungsNr'];
                $this->RechnungsDatum = $datarow['RechnungsDatum'];
                $this->SteuerNr = $datarow['SteuerNr'];
                $this->SteuerID = $datarow['SteuerID'];
                $this->Steuersatz = $datarow['Steuersatz'];

                $this->AbsFirma = $datarow['AbsFirma'];
                $this->AbsName = $datarow['AbsName'];
                $this->AbsStrasseNr = $datarow['AbsStrasseNr'];
                $this->AbsPLZOrt = $datarow['AbsPLZOrt'];
                $this->AbsTelefon = $datarow['AbsTelefon'];
                $this->AbsMobil = $datarow['AbsMobil'];
                $this->AbsInternet = $datarow['AbsInternet'];
                $this->AbsEmail = $datarow['AbsEmail'];

                $this->KunFirma = $datarow['KunFirma'];
                $this->KunName = $datarow['KunName'];
                $this->KunStrasseNr = $datarow['KunStrasseNr'];
                $this->KunPLZOrt = $datarow['KunPLZOrt'];
                $this->KunLand = $datarow['KunLand'];

                $this->Ueberschrift = $datarow['Ueberschrift'];
                $this->Freitext = $datarow['Freitext'];

                $this->Kontoinhaber = $datarow['Kontoinhaber'];
                $this->BankName = $datarow['BankName'];
                $this->IBAN = $datarow['IBAN'];
                $this->BIC = $datarow['BIC'];
            }
        }

        $sql = "SELECT * FROM Rechnungspositionen WHERE Rechnungsid = " . $RechnungsId;
        $query = mysqli_query($this->DBLink, $sql);

        if (!$query) {
            echo mysqli_error($this->DBLink);
        } else {
            while ($datarow = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
                $rechnungsposition = new position();
                $rechnungsposition->id = $datarow['id'];
                $rechnungsposition->RechnungsId = $datarow['RechnungsId'];
                $rechnungsposition->Menge = $datarow['Menge'];
                $rechnungsposition->Einheit = $datarow['Einheit'];
                $rechnungsposition->Bezeichnung = $datarow['Bezeichnung'];
                $rechnungsposition->Nettobetrag = $datarow['Nettobetrag'];
                $this->Positionen[$rechnungsposition->id] = $rechnungsposition;
            }
        }
    }

    function DateOut()
    {
        return date("d.m.Y", $this->RechnungsDatum);
    }

    function DateIn($DateString)
    {
        $myDate = time();
        $arr = explode('.', $DateString);
        if (count($arr) == 3) {
            if (strlen($arr[2]) == 2) $arr[2] = '20' . $arr[2];
            $myDate = strtotime(implode('.', $arr));
        }
        return $myDate;
    }

    private function SetUserData($Userdata)
    {
        $this->AdressenId = $Userdata['AdressenId'];

        $this->RechnungsNr = $Userdata['RechnungsNr'];
        $this->RechnungsDatum = $this->DateIn($Userdata['RechnungsDatum']);

        $this->KunFirma = $Userdata['KunFirma'];
        $this->KunName = $Userdata['KunName'];
        $this->KunStrasseNr = $Userdata['KunStrasseNr'];
        $this->KunPLZOrt = $Userdata['KunPLZOrt'];
        $this->KunLand = $Userdata['KunLand'];

        $this->Ueberschrift = $Userdata['Ueberschrift'];
        $this->Freitext = $Userdata['Freitext'];
    }

    private function Insert()
    {
        $sql = "INSERT INTO Rechnungen (AdressenId, RechnungsNr, RechnungsDatum, SteuerNr, SteuerID, Steuersatz, ";
        $sql .= "AbsFirma, AbsName, AbsStrasseNr, AbsPLZOrt, AbsTelefon, AbsMobil, AbsInternet, AbsEmail, ";
        $sql .= "KunFirma, KunName, KunStrasseNr, KunPLZOrt, KunLand, Ueberschrift, Freitext, ";
        $sql .= "Kontoinhaber, BankName, IBAN, BIC)";
        $sql .= " VALUES (" . $this->AdressenId . ", ";
        $sql .= "'" . $this->RechnungsNr . "', ";
        $sql .= $this->RechnungsDatum . ", ";
        $sql .= "'" . $this->SteuerNr . "', ";
        $sql .= "'" . $this->SteuerID . "', ";
        $sql .= $this->Steuersatz . ", ";

        $sql .= "'" . $this->AbsFirma . "', ";
        $sql .= "'" . $this->AbsName . "', ";
        $sql .= "'" . $this->AbsStrasseNr . "', ";
        $sql .= "'" . $this->AbsPLZOrt . "', ";
        $sql .= "'" . $this->AbsTelefon . "', ";
        $sql .= "'" . $this->AbsMobil . "', ";
        $sql .= "'" . $this->AbsInternet . "', ";
        $sql .= "'" . $this->AbsEmail . "', ";

        $sql .= "'" . $this->KunFirma . "', ";
        $sql .= "'" . $this->KunName . "', ";
        $sql .= "'" . $this->KunStrasseNr . "', ";
        $sql .= "'" . $this->KunPLZOrt . "', ";
        $sql .= "'" . $this->KunLand . "', ";

        $sql .= "'" . $this->Ueberschrift . "', ";
        $sql .= "'" . $this->Freitext . "', ";

        $sql .= "'" . $this->Kontoinhaber . "', ";
        $sql .= "'" . $this->BankName . "', ";
        $sql .= "'" . $this->IBAN . "', ";
        $sql .= "'" . $this->BIC . "')";
        $query = mysqli_query($this->DBLink, $sql);
        if (!$query) {
            echo mysqli_error($this->DBLink);
            return 0;
        }

        return mysqli_insert_id($this->DBLink);
    }

    private function Update($RechnungsId)
    {
        $sql = "UPDATE Rechnungen SET ";
        $sql .= "RechnungsNr = '" . $this->RechnungsNr . "', ";
        $sql .= "RechnungsDatum = " . $this->RechnungsDatum . ", ";

        $sql .= "KunFirma = '" . $this->KunFirma . "', ";
        $sql .= "KunName = '" . $this->KunName . "', ";
        $sql .= "KunStrasseNr = '" . $this->KunStrasseNr . "', ";
        $sql .= "KunPLZOrt = '" . $this->KunPLZOrt . "', ";
        $sql .= "KunLand = '" . $this->KunLand . "', ";

        $sql .= "Ueberschrift = '" . $this->Ueberschrift . "', ";
        $sql .= "Freitext = '" . $this->Freitext . "' ";

        $sql .= "WHERE id = " . $RechnungsId;
        $query = mysqli_query($this->DBLink, $sql);
        if (!$query) {
            echo mysqli_error($this->DBLink);
            return 0;
        }
        return $RechnungsId;
    }

    function Save($Userdata, $RechnungsId = 0)
    {
        $this->SetUserData($Userdata);
        if ($RechnungsId == 0) return $this->Insert();
        else return $this->Update($RechnungsId);
    }

    function Delete($RechnungsId) {
        $sql = "DELETE FROM Rechnungspositionen WHERE RechnungsId = " . $RechnungsId;
        $query = mysqli_query($this->DBLink, $sql);
        if (!$query) {
            echo mysqli_error($this->DBLink);
            return 0;
        }

        $sql = "DELETE FROM Rechnungen WHERE id = " . $RechnungsId;
        $query = mysqli_query($this->DBLink, $sql);
        if (!$query) {
            echo mysqli_error($this->DBLink);
            return 0;
        }

        return $RechnungsId;
    }

    function GetInvoiceHTML()
    {
        $Adressfeld = '<u>' . $this->AbsName . ' &bull; ' . $this->AbsStrasseNr . ' &bull; ' . $this->AbsPLZOrt . '</u><br><br>';
        $Adressfeld .= $this->KunFirma . '<br>';
        $Adressfeld .= $this->KunName . '<br>';
        $Adressfeld .= $this->KunStrasseNr . '<br>';
        $Adressfeld .= $this->KunPLZOrt;

        $html = '<table cellpadding="5" cellspacing="0" style="width: 100%; ">';
        $html .= '<tr>';
        $html .= '<td style="font-size:1.3em; font-weight: bold; width: 420px; ">' . $this->Ueberschrift . '</td>';
        $html .= '<td>';
        if (strlen($this->AbsInternet) > 1) $html .= 'Internet: ' . $this->AbsInternet . '<br>';
        else $html .= '&nbsp;<br>';
        if (strlen($this->AbsEmail) > 1) $html .= 'Email: ' . $this->AbsEmail . '<br>';
        else $html .= '&nbsp;<br>';
        if (strlen($this->AbsTelefon) > 1) $html .= 'Telefon: ' . $this->AbsTelefon . '<br>';
        else $html .= '&nbsp;<br>';
        if (strlen($this->AbsMobil) > 1) $html .= 'Mobil: ' . $this->AbsMobil;
        $html .= '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td colspan="2">' . trim($Adressfeld) . '</td>';
        $html .= '</tr>';
        $html .= '</table>';

        $html .= '<br><br><br>';

        $html .= '<table cellpadding="5" cellspacing="0" style="width: 100%; ">';
        $html .= '<tr>';
        $html .= '<td>' . $this->Ueberschrift . '-Nr.: ' . $this->RechnungsNr . '</td>';
        $html .= '<td>Datum: ' . date("d.m.Y", $this->RechnungsDatum) . '</td>';
        $html .= '<td style="text-align: right">SteuerNr: ' . $this->SteuerNr . '</td>';
        $html .= '</tr>';
        $html .= '</table>';

        $html .= '<br><br><br>';

        $html .= '<table cellpadding="5" cellspacing="0" border="0" style="width: 100%;">';
        $html .= '<tr style="background-color: #cccccc; padding:5px; ">';
        $html .= '<td width="460"><b>Bezeichnung</b></td>';
        $html .= '<td width="80"style="text-align: right;">&nbsp;</td>';
        $html .= '<td width="90" style="text-align: right;"><b>Preis</b></td>';
        $html .= '</tr>';
        $gesNetto = 0.00;
        foreach ($this->Positionen as $rpos) {
            $html .= '<tr>';
            $html .= '<td>' . $rpos->Bezeichnung . '</td>';
            if ($rpos->Menge > 0) $html .= '<td style="text-align: right;">' . $rpos->Menge . ' ' . $rpos->Einheit . '</td>';
            else $html .= '<td style="text-align: right;">&nbsp;</td>';
            $html .= '<td style="text-align: right;">' . $rpos->GetNetto() . ' €</td>';
            $html .= '</tr>';
            $gesNetto = $gesNetto + $rpos->Nettobetrag;
        }
        $html .= '<tr>';
        $gesMwSt = $gesNetto / 100 * $this->Steuersatz;
        $html .= '<td colspan="2" style="text-align: right;">Gesamt Netto</td>';
        $html .= '<td style="text-align: right; border-top: thin solid #cccccc;">' . number_format($gesNetto, 2, ',', '.') . ' €</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td colspan="2" style="text-align: right;">' . $this->Steuersatz . '% MwSt </td>';
        $html .= '<td style="text-align: right;">' . number_format($gesMwSt, 2, '.', ',') . ' €</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td colspan="2" style="text-align: right;"><b>Gesamt</b></td>';
        $html .= '<td style="text-align: right; border-top: thin solid #cccccc;"><b>' . number_format($gesMwSt + $gesNetto, 2, '.', ',') . ' €</b></td>';
        $html .= '</tr>';
        $html .= '</table>';

        $html .= '<br><br><br>';

        $html .= '<p>' . nl2br($this->Freitext) . '<p>';

        $html .= '<p>Überweisen Sie den Betrag bitte auf folgendes Konto<br>';
        $html .= $this->Kontoinhaber . '<br>';
        $html .= $this->BankName . '<br>';
        $html .= $this->IBAN . '<br>';
        $html .= $this->BIC . '</p>';

        return $html;
    }

    function Print($RechnungsId)
    {
        $this->Load($RechnungsId, '');
        // create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->setCreator(PDF_CREATOR);
        $pdf->setAuthor($this->AbsFirma);
        $pdf->setTitle($this->Ueberschrift);
        $pdf->setSubject('');
        $pdf->setKeywords('');

        // set default header data
        $pdf->setHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

        // set header and footer fonts
        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->setMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->setHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->setFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->setAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // ---------------------------------------------------------

        // set font
        $pdf->setFont('dejavusans', '', 10);

        // add a page
        $pdf->AddPage();

        // output the HTML content
        $pdf->writeHTML($this->GetInvoiceHTML(), true, false, true, false, '');


        // reset pointer to the last page
        $pdf->lastPage();

        // ---------------------------------------------------------

        $outputName = $this->RechnungsNr;
        $outputName.= '-' . date('d.m.Y', $this->RechnungsDatum);
        $outputName.= '-' . str_replace(' ', '_', $this->KunFirma);
        $pdf->Output(dirname(__FILE__, 2).'/rechnungen/'. $outputName . '.pdf', 'F');

        //Close and output PDF document
        $pdf->Output('invoice.pdf', 'I');
    }
}
