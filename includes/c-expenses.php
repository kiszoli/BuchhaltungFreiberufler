<?php
require_once("dbconfig.php");
require_once("c-expense.php");

class expenses
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
                    $myTable .= '<td class="tright"><a href="' . $_SERVER['PHP_SELF'] . '?delete=' . $datarow['id'] . '"> <img src="images/cross.png" alt="löschen"></a></td>' . PHP_EOL;
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

    function Delete($ExpenseId)
    {
        $myAusgabe = new expense();
        $myAusgabe->Delete($ExpenseId);
    }

    function Save($Userdata)
    {
        $myAusgabe = new expense();
        return $myAusgabe->Save($Userdata, $Userdata['id']);
    }
}
