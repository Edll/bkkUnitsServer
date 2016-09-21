<?php

class db {

    private $conn;

    private $rowNums;

    private $result;

    private $affectRows;

    private $stm;

    function connectDB () {
       // $this->conn = new mysqli("localhost", "root", "", "bkk-kleve");
         $this->conn = new mysqli("db-s1.rb-host.de", "bkk-kleve",
     "EvPafcohaHabewn+", "bkk-kleve");
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
            echo "Result großer 1: " . "<br>";
        } else {
            echo "insert" . "<br>";
            $insertWeeks = "INSERT INTO `weeks` ( `number`, `date`) VALUES (?, ?)";
            
            $stm = $this->getPreStm($insertWeeks);
            $stm->bind_param("ss", $number, $date);
            $stm->execute();
            $id = $stm->insert_id;
            $stm->close();
            
            echo "select week " . $id . "<br>";
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
                 "`name` like COALESCE(" . $this->msqli_set_null($name) .
                 ", `name`)";
        return $this->getResult($selectClass);
    }

    function insertFieldInfo ($data, $dataTyp, $weeksId, $hour, $classesId, $day) {
        $result = $this->selectFieldInfo(null, $data, $dataTyp, $weeksId, $classesId, $hour, $day);
        
        if ($this->getRowNums() >= 1) {
            return mysqli_fetch_array($result);
        } else {
            $insertFieldInfo = "INSERT INTO `fieldInfos` (`data`,`weeksId` , `hour` ,`dataTyp`, `classesId`, `day`) VALUES (?,?,?,?,?,?)";
            $stm = $this->getPreStm($insertFieldInfo);
            $stm->bind_param("siiiii", $data, $weeksId, $hour, $dataTyp, 
                    $classesId, $day);
            $stm->execute();
            $id = $stm->insert_id;
            $stm->close();
            
            $this->selectFieldInfo($id, null, null, null, null, null, null);
            return mysqli_fetch_array($this->result);
        }
    }

    function selectFieldInfo ($id, $data, $dataTyp, $weeksId, $classesId, $hour, 
            $day) {
        $selectHour = "SELECT * FROM `fieldInfos` WHERE " . "`id` = COALESCE(" .
                 $this->msqli_set_null($id) . ", `id`) AND" .
                 "`data` LIKE COALESCE(" . $this->msqli_set_null($data) .
                 ", `data`) AND " . "`classesId` LIKE COALESCE(" .
                 $this->msqli_set_null($classesId) . ", `classesId`) AND " .
                 "`weeksId` LIKE COALESCE(" . $this->msqli_set_null($weeksId) .
                 ", `weeksId`) AND " . "`hour` LIKE COALESCE(" .
                 $this->msqli_set_null($hour) . ", `hour`) AND " .
                 "`day` LIKE COALESCE(" . $this->msqli_set_null($day) .
                 ", `day`) AND " . "`dataTyp` LIKE COALESCE(" .
                 $this->msqli_set_null($dataTyp) . ", `dataTyp`) " .
                 "ORDER BY `day`, `hour`";
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
?>