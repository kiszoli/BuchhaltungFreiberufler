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

        $this->Datum = time();
    }

    function Load($ExpenseId)
    {
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

    private function CurrencyIn($CurrencyString)
    {
        $res = preg_replace("/[^0-9,-]/", "", $CurrencyString);
        if (substr($res, -1) == '-') $res = rtrim($res, '-');
        $count = 0;
        $res = preg_replace('/,+/', '.', $res, -1, $count);
        if ($count == 0) $res .= '.00';
        if (substr($res, -1) == '.') $res .= '00';
        if (substr($res, 0, 1) == '.') $res = '0' . $res;

        return number_format($res, 2, '.', '');
    }

    private function IntIn($IntString)
    {
        $res = preg_replace("/[^0-9]/", "", $IntString);
        return $res;
    }

    private function SetData($Userdata)
    {
        $this->id = $Userdata['id'];
        $this->Datum = strtotime($Userdata['Datum']);
        $this->Bezeichnung = $Userdata['Bezeichnung'];
        $this->MwSt = $this->IntIn($Userdata['MwSt']);
        if ($Userdata['Brutto'] != '' && $Userdata['Brutto'] != '0,00') $this->Brutto = $this->CurrencyIn($Userdata['Brutto']);
        else {
            $this->Brutto = $Userdata['Netto'] / 100 * $this->MwSt + $Userdata['Netto'];
        }
        $this->MwSt = $Userdata['MwSt'];
        $this->KontoId = $Userdata['KontoId'];
    }

    private function Insert()
    {
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

    private function Update()
    {
        $sql = "UPDATE Ausgaben SET ";
        $sql .= "Datum = " . $this->Datum . ", ";
        $sql .= "Bezeichnung = '" . $this->Bezeichnung . "', ";
        $sql .= "Brutto = " . $this->Brutto . ", ";
        $sql .= "MwSt = " . $this->MwSt . ", ";
        $sql .= "KontoId = " . $this->KontoId . " ";
        $sql .= "WHERE id = " . $this->id;
        $query = mysqli_query($this->DBLink, $sql);
        if (!$query) {
            echo mysqli_error($this->DBLink);
        }
        return $this->id;
    }

    function Save($Userdata)
    {
        $this->SetData($Userdata);
        if ($this->id > 0) return $this->Update();
        else return $this->Insert();
    }

    function Delete($ExpenseId)
    {
        $sql = "DELETE FROM Ausgaben WHERE id = " . $ExpenseId;
        $query = mysqli_query($this->DBLink, $sql);
        if (!$query) {
            echo mysqli_error($this->DBLink);
            return $ExpenseId;
        }
        return -1;
    }

    function GetEUER($BalanceYear, $SumIncome)
    {
        $myTable = '<table>' . PHP_EOL;

        $myTable .= '<thead>' . PHP_EOL;
        $myTable .= '<th width="100" class="tleft">KontoNr</th>' . PHP_EOL;
        $myTable .= '<th class="tleft">Bezeichnung</th>' . PHP_EOL;
        $myTable .= '<th width="80" class="tright">Summe</th>' . PHP_EOL;
        $myTable .= '<th width="80" class="tright">% Absetzbar</th>' . PHP_EOL;
        $myTable .= '<th width="80" class="tright">Ausgaben</th>' . PHP_EOL;
        $myTable .= '<th width="80" class="tright">Einnahmen</th>' . PHP_EOL;
        $myTable .= '</thead>' . PHP_EOL;

        $myTable .= '<tr>' . PHP_EOL;
        $myTable .= '<td></td>' . PHP_EOL;
        $myTable .= '<td></td>' . PHP_EOL;
        $myTable .= '<td></td>' . PHP_EOL;
        $myTable .= '<td></td>' . PHP_EOL;
        $myTable .= '<td></td>' . PHP_EOL;
        $myTable .= '<td class="tright"><b>' . $SumIncome . '<b></td>' . PHP_EOL;
        $myTable .= '</tr>' . PHP_EOL;

        $sql = "SELECT Konten.KontoNr, Konten.Bezeichnung, Sum(Ausgaben.Brutto) AS SummeBrutto, Ausgaben.MwSt, Konten.ProzentAbsetzbar";
        $sql .= " FROM Ausgaben INNER JOIN Konten ON Ausgaben.KontoId = Konten.id";
        $sql .= " WHERE (((Ausgaben.Datum)>=" . strtotime('01.01.' . $BalanceYear) . ") AND ((Ausgaben.Datum)<=" . strtotime('31.12.' . $BalanceYear) . "))";
        $sql .= " GROUP BY Konten.KontoNr, Konten.Bezeichnung, Ausgaben.MwSt, Konten.ProzentAbsetzbar";
        $query = mysqli_query($this->DBLink, $sql);

        $SumExpenses = 0.00;
        $SumExpensesFinal = 0.00;
        if (!$query) {
            echo mysqli_error($this->DBLink);
        } else {
            while ($datarow = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
                $myTable .= '<tr>' . PHP_EOL;
                $myTable .= '<td class="tleft">' . $datarow['KontoNr'] . '</td>' . PHP_EOL;
                $myTable .= '<td class="tleft">' . $datarow['Bezeichnung'] . '</td>' . PHP_EOL;

                $SumExpenses += $datarow['SummeBrutto'];
                $myTable .= '<td class="tright">' . number_format($datarow['SummeBrutto'], 2, ',', '.') . '</td>' . PHP_EOL;

                $myTable .= '<td class="tright">' . $datarow['ProzentAbsetzbar'] . '</td>' . PHP_EOL;

                $myAbsetzbar = $datarow['ProzentAbsetzbar'] / 100 * $datarow['SummeBrutto'];
                $SumExpensesFinal += $myAbsetzbar;
                $myTable .= '<td class="tright">' . number_format($myAbsetzbar, 2, ',', '.') . '</td>' . PHP_EOL;

                $myTable .= '<td></td>' . PHP_EOL;
                $myTable .= '</tr>' . PHP_EOL;
            }
        }

        $myTable .= '<tr>' . PHP_EOL;
        $myTable .= '<td></td>' . PHP_EOL;
        $myTable .= '<td></td>' . PHP_EOL;
        $myTable .= '<td style="text-align: right; border-top: thin solid #000;">' . number_format($SumExpenses, 2, ',', '.') . '</td>' . PHP_EOL;
        $myTable .= '<td></td>' . PHP_EOL;
        $myTable .= '<td style="text-align: right; border-top: thin solid #000;"><b>' . number_format($SumExpensesFinal, 2, ',', '.') . '</b></td>' . PHP_EOL;
        $myTable .= '<td></td>' . PHP_EOL;
        $myTable .= '</tr>' . PHP_EOL;
        $myTable .= '<tr>' . PHP_EOL;
        $myTable .= '<td></td>' . PHP_EOL;
        $myTable .= '<td></td>' . PHP_EOL;
        $myTable .= '<td></td>' . PHP_EOL;
        $myTable .= '<td></td>' . PHP_EOL;
        $myTable .= '<td class="tright"><b>Gewinn</b></td>' . PHP_EOL;
        $IncomeFinal = $SumIncome - $SumExpensesFinal;
        $myTable .= '<td style="text-align: right; border-top: thin solid #000;"><b>' . number_format($IncomeFinal, 2, ',', '.') . '</b></td>' . PHP_EOL;
        $myTable .= '</tr>' . PHP_EOL;

        $myTable .= "</table>" . PHP_EOL;

        return $myTable;
    }

    private function GetKontenDropdown($konten, $selectedId)
    {
        $myKonten = '<select name="userdata[KontoId]">' . PHP_EOL;
        foreach ($konten as $key => $value) {
            $myKonten .= '<option value="' . $key . '"';
            if ($key == $selectedId) $myKonten .= 'selected';
            $myKonten .= '>' . $value . "</option>";
        }
        $myKonten .= '</select';
        return $myKonten;
    }

    private function GetKonten()
    {
        $arr = array();
        $sql = "SELECT * FROM Konten ORDER BY Bezeichnung";
        $query = mysqli_query($this->DBLink, $sql);
        if (!$query) {
            echo mysqli_error($this->DBLink);
        } else {
            while ($datarow = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
                $arr[$datarow['id']] = $datarow['Bezeichnung'];
            }
        }
        return $arr;
    }

    function GetExpensesList($bilanzjahr, $ausgabenId)
    {
        $myKonten = $this->GetKonten();

        $myBilanzStart = (strtotime('01.01.' . $bilanzjahr));
        $myBilanzEnd = (strtotime('31.12.' . $bilanzjahr));
        $sql = "SELECT * FROM Ausgaben WHERE Datum >= " . $myBilanzStart . " AND Datum <= " . $myBilanzEnd . " ORDER BY Datum, KontoId, Bezeichnung";
        $query = mysqli_query($this->DBLink, $sql);

        $myTable = '';
        if (!$query) {
            echo mysqli_error($this->DBLink);
        } else {
            $myTable = '<table class="tout">' . PHP_EOL;
            $myTable .= '<thead>' . PHP_EOL;
            $myTable .= '<th>&nbsp;</th>' . PHP_EOL;
            $myTable .= '<th>Datum</th>' . PHP_EOL;
            $myTable .= '<th>Konto</th>' . PHP_EOL;
            $myTable .= '<th>Bezeichnung</th>' . PHP_EOL;
            $myTable .= '<th class="tright">Netto</th>' . PHP_EOL;
            $myTable .= '<th class="tright">MwSt</th>' . PHP_EOL;
            $myTable .= '<th class="tright">Brutto</th>' . PHP_EOL;
            $myTable .= '<th>&nbsp;</th>' . PHP_EOL;
            $myTable .= '</thead>' . PHP_EOL;
            $myTable .= '<tbody>' . PHP_EOL;
            while ($datarow = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
                if ($datarow['id'] == $ausgabenId) {
                    $myTable .= '<form action="' . $_SERVER['PHP_SELF'] . '" method="post" enctype="application/x-www-form-urlencoded">' . PHP_EOL;
                    $myTable .= '<tr>' . PHP_EOL;
                    $myTable .= '<td><input type="hidden" name="userdata[id]" value="' . $ausgabenId . '"></td>' . PHP_EOL;
                    $myTable .= '<td><input type="text" name="userdata[Datum]" value="' . date("d.m.Y", $datarow['Datum']) . '" />' . '</td>' . PHP_EOL;
                    $myTable .= '<td>' . $this->GetKontenDropdown($myKonten, $datarow['KontoId']) . '</td>' . PHP_EOL;
                    $myTable .= '<td> <input type="text" name="userdata[Bezeichnung]" value="' . $datarow['Bezeichnung'] . '" />' . '</td>' . PHP_EOL;
                    $myTable .= '<td class="tright"> <input type="text" name="userdata[Netto]" value="' . number_format($datarow['Brutto'] / (100 + $datarow['MwSt']) * 100, 2, ',', '.') . '" />' . '</td>' . PHP_EOL;
                    $myTable .= '<td class="tright"><input type="text" name="userdata[MwSt]" value="' . $datarow['MwSt'] . '" />' . '</td>' . PHP_EOL;
                    $myTable .= '<td class="tright"><input type="text" name="userdata[Brutto]" value="' . number_format($datarow['Brutto'], 2, ',', '.') . '" />' . '</td>' . PHP_EOL;
                    $myTable .= '<td> <input type="submit" name="save" value="Save" /></td>' . PHP_EOL;
                    $myTable .= '</tr>' . PHP_EOL;
                    $myTable .= '</form>' . PHP_EOL;
                } else {
                    $myTable .= '<tr>' . PHP_EOL;
                    $myTable .= '<td><input type="hidden" name ="userdata[id]" value="' . $datarow['id'] . '"></td>' . PHP_EOL;
                    $myTable .= '<td>' . date("d.m.Y", $datarow['Datum']) . '</td>' . PHP_EOL;
                    $myTable .= '<td>' . $myKonten[$datarow['KontoId']] . '</td>' . PHP_EOL;
                    $myTable .= '<td><a href="' . $_SERVER['PHP_SELF'] . '?update=' . $datarow['id'] . '">' . $datarow['Bezeichnung'] . '</a></td>' . PHP_EOL;
                    $myTable .= '<td class="tright">' . number_format($datarow['Brutto'] / (100 + $datarow['MwSt']) * 100, 2, ',', '.') . '</td>' . PHP_EOL;
                    $myTable .= '<td class="tright">' . $datarow['MwSt'] . '</td>' . PHP_EOL;
                    $myTable .= '<td class="tright">' . number_format($datarow['Brutto'], 2, ',', '.') . '</td>' . PHP_EOL;
                    $myTable .= '<td class="tright"><a href="' . $_SERVER['PHP_SELF'] . '?delete=' . $datarow['id'] . '"> <img src="images/trashbin.png" alt="löschen" onclick="return confirm(\'Are you sure you want to Remove?\');"></a></td>' . PHP_EOL;
                    $myTable .= '</tr>' . PHP_EOL;
                }
            }
            if ($ausgabenId == 0) {
                $myTable .= '<form action="' . $_SERVER['PHP_SELF'] . '" method="post" enctype="application/x-www-form-urlencoded">' . PHP_EOL;
                $myTable .= '<tr>' . PHP_EOL;
                $myTable .= '<td><input type="hidden" name ="userdata[id]" value="0"></td>' . PHP_EOL;
                $myTable .= '<td><input type="text" name="userdata[Datum]" value="" />' . '</td>' . PHP_EOL;
                $myTable .= '<td>' . $this->GetKontenDropdown($myKonten, 0) . '</td>' . PHP_EOL;
                $myTable .= '<td><input type="text" name="userdata[Bezeichnung]" value="" />' . '</td>' . PHP_EOL;
                $myTable .= '<td><input type="text" name="userdata[Netto]" value="" />' . '</td>' . PHP_EOL;
                $myTable .= '<td class="tright"><input type="text" name="userdata[MwSt]" value="19" />' . '</td>' . PHP_EOL;
                $myTable .= '<td class="tright"><input type="text" name="userdata[Brutto]" value="" />' . '</td>' . PHP_EOL;
                $myTable .= '<td class="tright"><input type="submit" name="save" value="Save" /></td>' . PHP_EOL;
                $myTable .= '</tr>' . PHP_EOL;
                $myTable .= '</form>' . PHP_EOL;
            }
            $myTable .= '</tbody>' . PHP_EOL;
            $myTable .= '</table>' . PHP_EOL;
        }

        return $myTable;
    }

    function GetExportList($Bilanzjahr)
    {
        $excelData[0] = array('Datum', 'Kontenrahmen', 'Bezeichnung', 'Netto', 'MwSt', 'Brutto');

        $myBilanzStart = (strtotime('01.01.' . $Bilanzjahr));
        $myBilanzEnd = (strtotime('31.12.' . $Bilanzjahr));

        $sql = "SELECT Ausgaben.id, Ausgaben.Datum, Konten.Bezeichnung AS Kontenrahmen, Ausgaben.Bezeichnung, Ausgaben.MwSt, Ausgaben.Brutto
        FROM Ausgaben LEFT JOIN Konten ON Ausgaben.KontoId = Konten.id
        WHERE (((Ausgaben.Datum)>=" . $myBilanzStart . ")) AND (((Ausgaben.Datum)<" . $myBilanzEnd . "))
        ORDER BY Ausgaben.Datum, Kontenrahmen, Ausgaben.Bezeichnung";

        $query = mysqli_query($this->DBLink, $sql);
        if (!$query) {
            echo mysqli_error($this->DBLink);
        } else {
            while ($datarow = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
                $excelData[$datarow['id']] = array(
                    date("d.m.Y", $datarow['Datum']),
                    $datarow['Kontenrahmen'],
                    $datarow['Bezeichnung'],
                    number_format($datarow['Brutto'] / (100 + $datarow['MwSt']) * 100, 2, ',', '.'),
                    $datarow['MwSt'],
                    $datarow['Brutto']
                );
            }
        }
        return $excelData;
    }

    function GetExportListEUER($Bilanzjahr, $SumIncome)
    {
        $excelData[0] = array('KontoNr', 'Bezeichnung', 'Netto €', 'Steuersatz %', 'MwSt €', 'Brutto €', 'Absetzbar %', 'Ausgaben Netto €', 'Ausgaben Brutto €', 'Einnahmen €');
        $excelData[1] = array('', '', '', '', '', '', '', '', '', $SumIncome);

        $myBilanzStart = (strtotime('01.01.' . $Bilanzjahr));
        $myBilanzEnd = (strtotime('31.12.' . $Bilanzjahr));

        $sql = "SELECT Konten.KontoNr, Konten.Bezeichnung, Sum(Ausgaben.Brutto) AS SummeBrutto, Ausgaben.MwSt, Konten.ProzentAbsetzbar";
        $sql .= " FROM Ausgaben INNER JOIN Konten ON Ausgaben.KontoId = Konten.id";
        $sql .= " WHERE (((Ausgaben.Datum)>=" . strtotime('01.01.' . $Bilanzjahr) . ") AND ((Ausgaben.Datum)<=" . strtotime('31.12.' . $Bilanzjahr) . "))";
        $sql .= " GROUP BY Konten.KontoNr, Konten.Bezeichnung, Ausgaben.MwSt, Konten.ProzentAbsetzbar";

        $i = 2;
        $sumNetto = 0;
        $sumBrutto = 0;
        $sumAusgaben = 0;
        $sumAusgabenNetto = 0;
        $sumMwSt = 0;
        $query = mysqli_query($this->DBLink, $sql);
        if (!$query) {
            echo mysqli_error($this->DBLink);
        } else {
            while ($datarow = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
                $netto = $datarow['SummeBrutto'] / (100 + $datarow['MwSt']) * 100;
                $mwst = $datarow['SummeBrutto'] - $netto;
                $sumNetto = $sumNetto + $netto;
                $sumMwSt = $sumMwSt + $mwst;
                $sumBrutto = $sumBrutto + $datarow['SummeBrutto'];
                $ausgaben = $datarow['ProzentAbsetzbar'] / 100 * $datarow['SummeBrutto'];
                $ausgabenNetto = $datarow['ProzentAbsetzbar'] / 100 * $netto;
                $sumAusgabenNetto = $sumAusgabenNetto + $ausgabenNetto;
                $sumAusgaben = $sumAusgaben + $ausgaben;
                $excelData[$i++] = array(
                    $datarow['KontoNr'],
                    $datarow['Bezeichnung'],
                    number_format($netto, 2, ',', '.'),
                    $datarow['MwSt'],
                    number_format($mwst, 2, ',', '.'),
                    number_format($datarow['SummeBrutto'], 2, ',', '.'),
                    $datarow['ProzentAbsetzbar'],
                    number_format($ausgabenNetto, 2, ',', '.'),
                    number_format($ausgaben, 2, ',', '.'),
                    '',
                );
            }
        }
        $excelData[$i++] = array(
            '', 
            '', 
            number_format($sumNetto, 2, ',', '.'), 
            '', 
            number_format($sumMwSt, 2, ',', '.'), 
            number_format($sumBrutto, 2, ',', '.'), 
            '', 
            number_format($sumAusgabenNetto, 2, ',', '.'),
            number_format($sumAusgaben, 2, ',', '.'),
            '');
        return $excelData;
    }
}
