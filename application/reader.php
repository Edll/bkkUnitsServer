<?php
include ('db.php');
include ('classes_import.php');
include ('weeks_import.php');
include ('plan_import.php');


function getData () {    

			
    $naviPath = "http://www.bkkleve.de/fileadmin/technik/infoplaene/schueler/frames/navbar.htm";
    // $naviPath =
    // "http://localhost/untis/fileadmin/technik/infoplaene/schueler/frames/navbar.htm";
    
    $msg = "Lese NaviBar " . $naviPath . "\n";
    
    $db = new db();
    $db->connectDB();
    
    $weeks = new read_weeks();
    $classes = new read_classes();
    $plan = new read_plan();
    
    foreach ($weeks->read($naviPath) as $WeekValue) {
       $msg = $msg . "Weeks\n";
        $weekResult = $db->insertWeeks($WeekValue[0], $WeekValue[1]);
        $msg = $msg . "--- Eintragen der Woche: " . $weekResult['number'] . "\n";
        
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
            $msg = $msg ."--- Eintragen der Klasse: " . $classResult['id'] . " " .
                     $classResult['name'] . "\n";
            
            $path_plan = "http://www.bkkleve.de/fileadmin/technik/infoplaene/schueler/" .
                     $weekResult['number'] . "/c/c000" . $classNumber . ".htm";
            // $path_plan =
            // "http://localhost/untis/fileadmin/technik/infoplaene/schueler/" .
            // $weekResult['number'] . "/c/c000" . $classNumber . ".htm";
            
            $msg = $msg . "Pfad: " . $path_plan . "\n";
            
            $planData = $plan->read($path_plan);
            
            $tagColumeCounter = 0;
            
            foreach ($planData as $hourValue) {
                
                foreach ($hourValue as $dayValue) {
                    if ($tagColumeCounter != 0) {}
                    $stunde;
                    $fieldTypCounter = 0;
                    
                    foreach ($dayValue as $fildValue) {
                        if ($tagColumeCounter == 0) {
                            $stunde = $dataFields = preg_replace('/\s+/', '', 
                                    $fildValue);
                        } else {
                            if ($fildValue !== "") {
                                $db->insertFieldInfo(
                                        preg_replace('/\s+/', ' ', $fildValue), 
                                        $fieldTypCounter, $weekResult['id'], 
                                        $stunde, $classResult['id'], 
                                        $tagColumeCounter);
                            }
                        }
                        $fieldTypCounter ++;
                    }
                    $tagColumeCounter ++;
                }
                $tagColumeCounter = 0;
            }
        }
    }
    $db->closeDB();
    return $msg;
}

try {
	$datum = date("d.m.Y");
	$uhrzeit = date("H:i");
	$msg = "Start!". $datum." - ".$uhrzeit." Uhr \n";
	
	
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
   $msg = $msg . getData();
} catch (Exception $e) {
    $msg = $msg . 'Exception abgefangen: '. $e->getMessage() . "\n";
}

$datum = date("d.m.Y");
$uhrzeit = date("H:i");
$msg = $msg . "Ende!". $datum." - ".$uhrzeit." Uhr \n";

mail('junk@edlly.de', 'Reader durchgelaufen', $msg);

?>
	