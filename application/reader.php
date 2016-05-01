<?php

class read_classes {

    function read ($path) {
        $file = file_get_contents($path);
        $search_string_start = "var classes = [";
        $search_string_end = " var flcl = 1; var flte = 1;";
        
        $start = strpos($file, $search_string_start);
        $start = $start + strlen($search_string_start);
        
        $end = strpos($file, $search_string_end);
        
        $classes_string = substr($file, $start, ($end - $start - 4));
        $classes_string = str_replace("\"", "", $classes_string);
        $classes = explode(",", $classes_string);
        
        return $classes;
    }
}

class read_weeks {

    function read ($path) {
        $file = file_get_contents($path);
        
        $search_string_start = "<select name=\"week\" class=\"selectbox\" onChange=\"doDisplayTimetable(NavBar, topDir);\">";
        $search_string_end = "<select name=\"type\" class=\"selectbox\" onChange=\"ChangeElementOptions(NavBar);\">";
        
        $start = strpos($file, $search_string_start);
        $start = $start + strlen($search_string_start);
        
        $end = strpos($file, $search_string_end);
        
        $week_string = substr($file, $start, ($end - $start - 286));
        
        $week_string = str_replace("<", "", $week_string);
        $week_string = str_replace(">", "", $week_string);
        $week_string = str_replace("option", "", $week_string);
        $week_string = str_replace("/", "", $week_string);
        $week_string = str_replace("value=", "", $week_string);
        $week_string = str_replace("\"", " ", $week_string);
        
        // TODO Weeks Value must be convert to int and than reduce -1 but check
        // it in the raw datas
        $rawData = explode(" ", $week_string);
        
        unset($rawData[0]);
        unset($rawData[1]);
        unset($rawData[4]);
        
        // zerlegen in nummer und date und speichern in Array
        $dataCounter = 0;
        $resultCounter = 0;
        $result;
        $weeks;
        foreach ($rawData as $dataFields) {
            if ($dataCounter % 2 != 1) {
                $weeks[0] = $dataFields;
            } else {
                $dataFields = preg_replace('/\s+/', '', $dataFields);
                $weeks[1] = $dataFields;
                $result[$resultCounter] = $weeks;
                $resultCounter ++;
            }
            
            $dataCounter ++;
        }
        
        return $result;
    }
}

class read_plan {

    function read ($path) {
        $file = file_get_contents($path);
        
        $dom = new DOMDocument();
        // @ is suppress for html parse failure. The Document is not a W3C file.
        @$dom->loadHTML($file);
        
        $nodeValue;
        $days;
        
        foreach ($dom->getElementsByTagName("table") as $table) {
            // $nodeValue [] = $table->nodeValue;
            $nodeValue[] = $table;
        }
        // echo $counter;
        // remove erstes element das ist die ober Table
        unset($nodeValue[0]);
        
        // remove montag - freitag
        for ($i = 0; $i < 8; $i ++) {
            unset($nodeValue[$i]);
        }
        $dataCounter = 0;
        $stundeCounter = 0;
        $tagCounter = 0;
        
        $tagArray;
        $stundenArray;
        $fieldInfoArray;
        
        foreach ($nodeValue as $value) {
            
            // Reset der FieldInfo Daten
            unset($fieldInfoArray);
            
            // TagCounter ist null Stunden Spalte
            if ($tagCounter == 0) {
                $fieldInfoArray[0] = $value->nodeValue;
            } else {
                // Durchgehen der Tage von Montag bis Samstag
                
                // Abfrage der td Felder in einem Info feld um diese dann
                // auszugeben
                $counterTd = 0;
                foreach ($value->getElementsByTagName("tr") as $td) {
                    $fieldInfoArray[$counterTd] = $td->nodeValue;
                    $counterTd ++;
                }
            }
            
            // Eintragen der Daten in das Rückgabe Array
            $tagArray[$tagCounter] = $fieldInfoArray;
            $stundenArray[$stundeCounter] = $tagArray;
            
            // Durchzählen der Tabel struktur
            $dataCounter ++;
            if ($dataCounter % 7 == 0) {
                $stundeCounter ++;
                $tagCounter = 0;
            } else {
                $tagCounter ++;
            }
        }
        $size = count($stundenArray);
        
        // entfernen der 14 stunden. wo die her kommt ist unklar. Offenbar aus
        // dem DOM model.
        unset($stundenArray[$size - 1]);
        
        return $stundenArray;
    }
}

function getData () {
    $naviPath = "http://localhost/untis/fileadmin/technik/infoplaene/schueler/frames/navbar.htm";
    
    $db = new db();
    $db->connectDB();
    
    $weeks = new read_weeks();
    $classes = new read_classes();
    $plan = new read_plan();
    
    foreach ($weeks->read($naviPath) as $WeekValue) {
        $weekResult = $db->insertWeeks($WeekValue[0], $WeekValue[1]);
        echo "--- Eintragen der Woche: " . $weekResult['number'] . "</br>";
        
        $classCounter = 0;
        
        foreach ($classes->read($naviPath) as $classData) {
            $classCounter ++;
            $classNumber;
            
            if ($classCounter < 10) {
                $classNumber = "0" . $classCounter;
            } else {
                $classNumber = $classCounter;
            }
            
            $classResult = $db->insertClass($weekResult['id'], $classNumber, 
                    $classData);
            echo "--- Eintragen der Klasse: " . $classResult['id'] . " " .
                     $classResult['name'] . "</br>";
            
            $path_plan = "http://localhost/untis/fileadmin/technik/infoplaene/schueler/" .
                     $weekResult['number'] . "/c/c000" . $classNumber . ".htm";
            
            echo "Pfad: " . $path_plan . "</br>";
            
            $planData = $plan->read($path_plan);
            
            $counter = 0;
            $counter2 = 0;
            $days = array(
                    "Montag",
                    "Dienstag",
                    "Mittwoch",
                    "Donnerstag",
                    "Freitag",
                    "Samstag",
                    "Sonntag"
            );
            $hourId;
            foreach ($planData as $hourValue) {
                
                foreach ($hourValue as $dayValue) {
                    if ($counter2 == 0) {
                        echo "Stunde: ";
                    } else {
                        echo "Tage: " . $days[$counter2 - 1];
                        echo "</br>";
                    }
                    $stunde;
                    $fieldTypCounter = 0;
                    
                    foreach ($dayValue as $fildValue) {
                        if ($counter2 == 0) {
                            echo "Stunde: ";
                            echo $fildValue;
                            echo "</br>";
                            $stunde = $dataFields = preg_replace('/\s+/', '', 
                                    $fildValue);
                        } else {
                            echo $fildValue."</br>";
                            $db->insertFieldInfo($hourId, preg_replace('/\s+/', ' ', $fildValue), $fieldTypCounter);
                        }
                        $fieldTypCounter ++;
                    }
                    $hourResult = $db->insertHour($classResult['id'], 
                            $weekResult['id'], $stunde, $counter2);
                    $hourId = $hourResult['id'];
                    $counter2 ++;
                }
                $counter2 = 0;
                $counter ++;
            }
            break;
        }
        break;
    }
    $db->closeDB();
}

class db {

    private $conn;

    private $rowNums;

    private $result;

    private $affectRows;

    private $stm;

    function connectDB () {
        $this->conn = new mysqli("localhost", "root", "", "bkkUnits");
    }

    function getResult ($query) {
        $this->doResult($query);
        return $this->result;
    }

    function getRowNums () {
        $this->doRowNums();
        return $this->rowNums;
    }

    function closeDB () {
        $this->conn->close();
    }

    function getAffectRows () {
        $this->doAffectRows();
        return $this->affectRows;
    }

    function getPreStm ($query) {
        $this->doPreStm($query);
        return $this->stm;
    }

    function insertWeeks ($number, $date) {
        $result = $this->selectWeek(null, $number, $date);
        
        if ($this->getRowNums() >= 1) {
            return mysqli_fetch_array($result);
        } else {
            $insertWeeks = "INSERT INTO `weeks` ( `number`, `date`) VALUES (?, ?)";
            
            $stm = $this->getPreStm($insertWeeks);
            $stm->bind_param("ss", $number, $date);
            $stm->execute();
            $id = $stm->insert_id;
            $stm->close();
            
            $this->selectWeek($id, null, null);
            return mysqli_fetch_array($this->result);
        }
    }

    function selectWeek ($id, $number, $date) {
        $selectWeeks = "SELECT * FROM `weeks` WHERE " . "`id` = COALESCE(" .
                 $this->msqli_set_null($id) . ", `id`) AND" .
                 "`number` LIKE COALESCE(" . $this->msqli_set_null($number) .
                 ", `number`) AND " . "`date` LIKE COALESCE(" .
                 $this->msqli_set_null($date) . ", `date`)";
        
        return $this->getResult($selectWeeks);
    }

    function insertClass ($weeksId, $number, $name) {
        $result = $this->selectClass(null, $weeksId, $number, $name);
        
        if ($this->getRowNums() >= 1) {
            return mysqli_fetch_array($result);
        } else {
            
            $insertClass = "INSERT INTO `classes` (`weeksId`, `number`, `name`) VALUES ( ?,?,?)";
            $stm = $this->getPreStm($insertClass);
            $stm->bind_param("iss", $weeksId, $number, $name);
            $stm->execute();
            $id = $stm->insert_id;
            $stm->close();
            
            $this->selectClass($id, null, null, null);
            return mysqli_fetch_array($this->result);
        }
    }

    function selectClass ($id, $weeksId, $number, $name) {
        $selectClass = "SELECT * FROM `classes` WHERE " . "`id` = COALESCE(" .
                 $this->msqli_set_null($id) . ", `id`) AND" .
                 "`weeksId` = COALESCE(" . $this->msqli_set_null($weeksId) .
                 ", `weeksId`) AND" . "`number` = COALESCE(" .
                 $this->msqli_set_null($number) . ", `number`) AND" .
                 "`name` = COALESCE(" . $this->msqli_set_null($name) .
                 ", `name`)";
        return $this->getResult($selectClass);
    }

    function insertHour ($classesId, $weeksId, $hour, $day) {
        $result = $this->selectHour(null, $classesId, $weeksId, $hour, $day);
        
        if ($this->getRowNums() >= 1) {
            return mysqli_fetch_array($result);
        } else {
            $insertHour = "INSERT INTO `hours` (`classesId`, `weeksId`, `hour`, `day`) VALUES (?,?,?,?)";
            $stm = $this->getPreStm($insertHour);
            $stm->bind_param("iiii", $classesId, $classesId, $hour, $day);
            $stm->execute();
            $id = $stm->insert_id;
            $stm->close();
            
            $this->selectHour($id, null, null, null, null);
            return mysqli_fetch_array($this->result);
        }
    }

    function selectHour ($id, $classesId, $weeksId, $hour, $day) {
        $selectHour = "SELECT * FROM `hours` WHERE " . "`id` = COALESCE(" .
                 $this->msqli_set_null($id) . ", `id`) AND" .
                 "`classesId` LIKE COALESCE(" . $this->msqli_set_null(
                        $classesId) . ", `classesId`) AND " .
                 "`weeksId` LIKE COALESCE(" . $this->msqli_set_null($weeksId) .
                 ", `weeksId`) AND " . "`hour` LIKE COALESCE(" .
                 $this->msqli_set_null($hour) . ", `hour`) AND " .
                 "`day` LIKE COALESCE(" . $this->msqli_set_null($day) .
                 ", `day`) ";
        return $this->getResult($selectHour);
    }
    
    function insertFieldInfo ($hoursId, $data, $dataTyp) {
        $result = $this->selectFieldInfo(null, $hoursId, $data, $dataTyp);
    
        if ($this->getRowNums() >= 1) {
            return mysqli_fetch_array($result);
        } else {
            $insertFieldInfo = "INSERT INTO `fieldInfos` (`hoursId`, `data`, `dataTyp`) VALUES (?,?,?)";
            $stm = $this->getPreStm($insertFieldInfo);
            $stm->bind_param("isi", $hoursId, $data, $dataTyp);
            $stm->execute();
            $id = $stm->insert_id;
            $stm->close();
    
            $this->selectFieldInfo($id, null, null, null);
            return mysqli_fetch_array($this->result);
        }
    }
    
    function selectFieldInfo ($id, $hoursId, $data, $dataTyp) {
        $selectHour = "SELECT * FROM `fieldInfos` WHERE " .
                "`id` = COALESCE(" .$this->msqli_set_null($id) . ", `id`) AND" .
                "`hoursId` LIKE COALESCE(" . $this->msqli_set_null( $hoursId) . ", `hoursId`) AND " .
                "`data` LIKE COALESCE(" . $this->msqli_set_null($data) . ", `data`) AND " . 
                "`dataTyp` LIKE COALESCE(" . $this->msqli_set_null($dataTyp) . ", `dataTyp`) ";
        return $this->getResult($selectHour);
    }
    
    
  

    private function doResult ($query) {
        $this->result = $this->conn->query($query);
    }

    private function doRowNums () {
        if (! $this->result) {
            $this->rowNums = 0;
        } else {
            $this->rowNums = $this->result->num_rows;
        }
    }

    private function doAffectRows () {
        $this->affectRows = $this->conn->affected_rows;
    }

    private function doPreStm ($query) {
        $this->stm = $this->conn->prepare($query);
    }

    private function msqli_set_null ($param) {
        if ($param === null) {
            $param = "null";
        } else {
            $param = "'" . $param . "'";
        }
        return $param;
    }
}

getData();

$db = new db();
$db->connectDB();

$result = $db->selectWeek(null, null, null);

while ($row = mysqli_fetch_array($result)) {
    echo $row['date'];
    echo "</br>";
}

$db->closeDB();

?>
	