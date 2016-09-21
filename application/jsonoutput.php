<?php
include ('db.php');
header('Content-Type: application/json');

$db = new db();
$db->connectDB();

$jsonOutput = "{\"data\":\"no return\"}";
if (array_key_exists("field", $_GET) && array_key_exists("class", $_GET)) {
    if ($_GET["field"] == "all" && $_GET["class"] == "all") {
        
        $result = $db->selectFieldInfo(null, null, null, null, null, null, null);
        $jsonOutput = "{\"timetables\":[";
        while ($row = mysqli_fetch_array($result)) {
            $jsonOutput = $jsonOutput . json_encode($row);
            $jsonOutput = $jsonOutput . ",";
        }
        $jsonOutput = rtrim($jsonOutput, ",");
        $jsonOutput = $jsonOutput . "]}";
    } else {
        $result = $db->selectFieldInfo(null, null, null, $_GET["field"], 
                $_GET["class"], null, null);
        $jsonOutput = "{\"timetables\":[";
        
        while ($row = mysqli_fetch_array($result)) {
            $jsonOutput = $jsonOutput . json_encode($row);
            $jsonOutput = $jsonOutput . ",";
        }
        $jsonOutput = rtrim($jsonOutput, ",");
        $jsonOutput = $jsonOutput . "]}";
    }
} else 
    if (array_key_exists("class", $_GET)) {
        if ($_GET["class"] == "this") {
            $result = $db->selectClass(null, $_GET["week"], null, null);
            $jsonOutput = "{\"classes\":[";
            while ($row = mysqli_fetch_array($result)) {
                $jsonOutput = $jsonOutput . json_encode($row);
                $jsonOutput = $jsonOutput . ",";
            }
            $jsonOutput = rtrim($jsonOutput, ",");
            $jsonOutput = $jsonOutput . "]}";
        }else if ($_GET["class"] == "all") {
            $result = $db->selectClass(null, null, null, null);
            $jsonOutput = "{\"classes\":[";
            while ($row = mysqli_fetch_array($result)) {
                $jsonOutput = $jsonOutput . json_encode($row);
                $jsonOutput = $jsonOutput . ",";
            }
            $jsonOutput = rtrim($jsonOutput, ",");
            $jsonOutput = $jsonOutput . "]}";
        } else {
            $result = $db->selectClass(null, null, null, $_GET["class"]);
            $jsonOutput = "{\"classes\":[";
            while ($row = mysqli_fetch_array($result)) {
                $jsonOutput = $jsonOutput . json_encode($row);
                $jsonOutput = $jsonOutput . ",";
            }
            $jsonOutput = rtrim($jsonOutput, ",");
            $jsonOutput = $jsonOutput . "]}";
        }
    } else 
        if (array_key_exists("week", $_GET)) {
            
            if ($_GET["week"] == "all") {
                $result = $db->selectWeek(null, null, null);
                $jsonOutput = "{\"weeks\":[";
                
                while ($row = mysqli_fetch_array($result)) {
                    $jsonOutput = $jsonOutput . json_encode($row);
                    $jsonOutput = $jsonOutput . ",";
                }
                $jsonOutput = rtrim($jsonOutput, ",");
                $jsonOutput = $jsonOutput . "]}";
            } else 
                if ($_GET["week"] !== null) {
                    $result = $db->selectWeek(null, $_GET["week"], null);
                    $jsonOutput = "";
                    $jsonOutput = "{\"weeks\":[";
                    while ($row = mysqli_fetch_array($result)) {
                        $jsonOutput = $jsonOutput . json_encode($row);
                        $jsonOutput = $jsonOutput . ",";
                    }
                    $jsonOutput = rtrim($jsonOutput, ",");
                    $jsonOutput = $jsonOutput . "]}"; 
                }
        } else 
            if (array_key_exists("field", $_GET)) {
                if ($_GET["field"] == "all") {
                    $result = $db->selectFieldInfo(null, null, null, null, null, 
                            null, null);
                    $jsonOutput = "{\"timetables\":[";
                    while ($row = mysqli_fetch_array($result)) {
                        $jsonOutput = $jsonOutput . json_encode($row);
                        $jsonOutput = $jsonOutput . ",";
                    }
                    $jsonOutput = rtrim($jsonOutput, ",");
                    $jsonOutput = $jsonOutput . "]}";
                } else {
                    
                    $result = $db->selectFieldInfo(null, null, null, 
                            $_GET["field"], null, null, null);
                    $jsonOutput = "{\"timetables\":[";
                    while ($row = mysqli_fetch_array($result)) {
                        $jsonOutput = $jsonOutput . json_encode($row);
                        $jsonOutput = $jsonOutput . ",";
                    }
                    $jsonOutput = rtrim($jsonOutput, ",");
                    $jsonOutput = $jsonOutput . "]}";
                }
            }
echo $jsonOutput;
?>