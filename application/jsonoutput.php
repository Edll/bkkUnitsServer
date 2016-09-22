<?php
include ('db.php');

define("GET_FUNC", "func");
define("GET_FUNC_WEEK", "week");
define("GET_FUNC_CLASS", "class");
define("GET_FUNC_TIMETABLE", "timetable");

define("GET_WEEKSID", "week_id");
define("GET_CLASSID", "class_id");
define("GET_TIMETABLEID", "timetable_id");
define("GET_ALL_DATA", "all");

start_output();

function start_output () {
    header('Content-Type: application/json');
    
    $db = new db();
    $db->connectDB();
    $jsonOutput = call_funktion($db);
    echo $jsonOutput;
    $db->closeDB();
}

function call_funktion (db $db) {
    $jsonOutput = "";
    if (isset($_GET[GET_FUNC])) {
        $func = filter_get($db, GET_FUNC);
        
        if (empty($func)) {
            $jsonOutput = show_error_no_selection();
        } else {
            switch ($func) {
                case GET_FUNC_WEEK:
                    $jsonOutput = show_week_selection($db);
                    break;
                case GET_FUNC_CLASS:
                    $jsonOutput = show_class_selection($db);
                    break;
                case GET_FUNC_TIMETABLE:
                    $jsonOutput = show_timetable_selection($db);
                    break;
                default:
                    $jsonOutput = show_error_no_selection();
                    break;
            }
        }
    }
    return $jsonOutput;
}

function show_error_no_selection () {
    return "{\"error\":\"no function selected\"}";
}

function show_week_selection (db $db) {
    $result = NULL;
    $json = "{\"error\":\"no week selected\"}";
    
    if (isset($_GET[GET_WEEKSID])) {
        $weekId = filter_get($db, GET_WEEKSID);
        
        if ($weekId == GET_ALL_DATA) {
            $result = $db->selectWeek(null, null, null);
        } else 
            if (! empty($weekId)) {
                $result = $db->selectWeek($weekId, null, null);
            }
    }
    if ($result != NULL) {
        $json = create_json_output($result, "weeks");
    }
    
    return $json;
}

function show_class_selection (db $db) {
    $result = NULL;
    $json = "{\"error\":\"no class selected\"}";
    
    if (isset($_GET[GET_WEEKSID])) {
        $weekId = filter_get($db, GET_WEEKSID);
        if (! empty($weekId)) {
            $result = $db->selectClass(null, $weekId, null, null);
        }
    }
    
    if (isset($_GET[GET_CLASSID])) {
        $classid = filter_get($db, GET_CLASSID);
        if ($classid == GET_ALL_DATA) {
            $result = $db->selectClass(null, null, null, null);
        } else 
            if (! empty($classid)) {
                $result = $db->selectClass($classid, null, null, null);
            }
    }
    if ($result != NULL) {
        $json = create_json_output($result, "classes");
    }
    return $json;
}

function show_timetable_selection (db $db) {
    $result = NULL;
    $json = "{\"error\":\"no timetable selected\"}";
    
    if (isset($_GET[GET_WEEKSID]) && isset($_GET[GET_CLASSID])) {
        $weekId = filter_get($db, GET_WEEKSID);
        $classid = filter_get($db, GET_CLASSID);
        
        if ($weekId == GET_ALL_DATA && $classid == GET_ALL_DATA) {
            $result = $db->selectFieldInfo(null, null, null, null, null, null, 
                    null);
        } else {
            $result = $db->selectFieldInfo(null, null, null, $weekId, $classid, 
                    null, null);
        }
    } else 
        if (isset($_GET[GET_WEEKSID])) {
            $weekId = filter_get($db, GET_WEEKSID);
            $result = $db->selectFieldInfo(null, null, null, $weekId, null, 
                    null, null);
        } else 
            if (isset($_GET[GET_TIMETABLEID])) {
                $timetableId = filter_get($db, GET_TIMETABLEID);
                $result = $db->selectFieldInfo($timetableId, null, null, null, 
                        null, null, null);
            }
    
    if ($result != NULL) {
        $json = create_json_output($result, "timetables");
    }
    
    return $json;
}

function filter_get (db $db, string $get_name) {
    $data = filter_input(INPUT_GET, $get_name, FILTER_SANITIZE_ENCODED);
    $data = $db->mysqli_real_escape_string($data);
    return $data;
}

function create_json_output ($result, $json_prefix) {
    $json = "{\"" . $json_prefix . "\":[";
    
    while ($row = mysqli_fetch_array($result)) {
        $json = $json . json_encode($row);
        $json = $json . ",";
    }
    $json = rtrim($json, ",");
    $json = $json . "]}";
    return $json;
}
?>