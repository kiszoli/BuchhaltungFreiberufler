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
    }

    function Load($AccountId)
    {
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

    private function SetData($Userdata)
    {
        $this->id = $Userdata['id'];
        $this->KontoNr = $Userdata['KontoNr'];
        $this->Bezeichnung = $Userdata['Bezeichnung'];
        $this->ProzentAbsetzbar = $Userdata['ProzentAbsetzbar'];
    }

    private function Insert()
    {
        $sql = "INSERT INTO Konten (KontoNr, Bezeichnung, ProzentAbsetzbar)";
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

    private function Update()
    {
        $sql = "UPDATE Konten SET ";
        $sql .= "KontoNr = '" . $this->KontoNr . "', ";
        $sql .= "Bezeichnung = '" . $this->Bezeichnung . "', ";
        $sql .= "ProzentAbsetzbar = " . $this->ProzentAbsetzbar;
        $sql .= " WHERE id = " . $this->id;
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

    function Delete($KontenId)
    {
        $sql = "DELETE FROM Konten WHERE id = " . $KontenId;
        $query = mysqli_query($this->DBLink, $sql);
        if (!$query) {
            echo mysqli_error($this->DBLink);
            return $KontenId;
        }
        return 0;
    }

    function GetAccountList($KontenId)
    {
        $sql = "SELECT * FROM Konten ORDER BY KontoNr, Bezeichnung";
        $query = mysqli_query($this->DBLink, $sql);

        $myTable = '';
        if (!$query) {
            echo mysqli_error($this->DBLink);
        } else {
            $myTable = '<div class="smallcontent">';
            $myTable .= '<table>' . PHP_EOL;
            $myTable .= '<thead>' . PHP_EOL;
            $myTable .= '<th>Kontenrahmen</th>' . PHP_EOL;
            $myTable .= '<th style="width:70%">Bezeichnung</th>' . PHP_EOL;
            $myTable .= '<th>%Absetzbar</th>' . PHP_EOL;
            $myTable .= '<th>&nbsp;</th>' . PHP_EOL;
            $myTable .= '</thead>' . PHP_EOL;
            $myTable .= '<tbody>' . PHP_EOL;

            while ($datarow = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
                if ($datarow['id'] == $KontenId) {
                    $myTable .= '<form action="' . $_SERVER['PHP_SELF'] . '" method="post" enctype="application/x-www-form-urlencoded">' . PHP_EOL;
                    $myTable .= '<tr>' . PHP_EOL;
                    $myTable .= '<td><input type="hidden" name="userdata[id]" value="' . $KontenId . '"><input type="text" name="userdata[KontoNr]" value="' . $datarow['KontoNr'] . '" />' . '</td>' . PHP_EOL;
                    $myTable .= '<td><input type="text" name="userdata[Bezeichnung]" value="' . $datarow['Bezeichnung'] . '" />' . '</td>' . PHP_EOL;
                    $myTable .= '<td><input type="text" name="userdata[ProzentAbsetzbar]" value="' . $datarow['ProzentAbsetzbar'] . '" />' . '</td>' . PHP_EOL;
                    $myTable .= '<td><input type="submit" name="save" value="Save" /></td>' . PHP_EOL;
                    $myTable .= '</tr>' . PHP_EOL;
                    $myTable .= '</form>' . PHP_EOL;
                } else {
                    $myTable .= '<tr>' . PHP_EOL;
                    $myTable .= '<td>' . $datarow['KontoNr'] . '</td>' . PHP_EOL;
                    $myTable .= '<td><a href="' . $_SERVER['PHP_SELF'] . '?update=' . $datarow['id'] . '">' . $datarow['Bezeichnung'] . '</a></td>' . PHP_EOL;
                    $myTable .= '<td class="tright">' . $datarow['ProzentAbsetzbar'] . '</td>' . PHP_EOL;
                    $myTable .= '<td><a href="' . $_SERVER['PHP_SELF'] . '?delete=' . $datarow['id'] . '" onclick="return confirm(\'Are you sure you want to Remove?\');"><img src="images/trashbin.png"></a></td>' . PHP_EOL;
                    $myTable .= '</tr>' . PHP_EOL;
                }
            }
            if ($KontenId == 0) {
                $myTable .= '<form action="' . $_SERVER['PHP_SELF'] . '" method="post" enctype="application/x-www-form-urlencoded">' . PHP_EOL;
                $myTable .= '<tr>' . PHP_EOL;
                $myTable .= '<td><input type="hidden" name ="userdata[id]" value="0"><input type="text" name="userdata[KontoNr]" value="" />' . '</td>' . PHP_EOL;
                $myTable .= '<td><input type="text" name="userdata[Bezeichnung]" value="" />' . '</td>' . PHP_EOL;
                $myTable .= '<td class="tright"><input type="text" name="userdata[ProzentAbsetzbar]" value="" />' . '</td>' . PHP_EOL;
                $myTable .= '<td class="tright"><input type="submit" name="save" value="Save" /></td>' . PHP_EOL;
                $myTable .= '</tr>' . PHP_EOL;
                $myTable .= '</form>' . PHP_EOL;
            }
            $myTable .= '</tbody>' . PHP_EOL;

            $myTable .= '</table>' . PHP_EOL;
            $myTable .= '</div>';
        }

        return $myTable;
    }
}
