<?php

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
        
        echo "Lese Datenfeld: " . $week_string . "<br>";
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
            echo "Lese Datenfeld: " . $dataFields . "<br>";
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

?>