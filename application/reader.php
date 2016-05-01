<?php
include ('db.php');
include ('classes_import.php');
include ('weeks_import.php');
include ('plan_import.php');

function getData () {
    $naviPath = "http://www.bkkleve.de/fileadmin/technik/infoplaene/schueler/frames/navbar.htm";
    
    // $naviPath =
    // "http://localhost/untis/fileadmin/technik/infoplaene/schueler/frames/navbar.htm";
    
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
            
            $path_plan = "http://www.bkkleve.de/fileadmin/technik/infoplaene/schueler/" .
                     $weekResult['number'] . "/c/c000" . $classNumber . ".htm";
            // $path_plan =
            // "http://localhost/untis/fileadmin/technik/infoplaene/schueler/" .
            // $weekResult['number'] . "/c/c000" . $classNumber . ".htm";
            
            echo "Pfad: " . $path_plan . "</br>";
            
            $planData = $plan->read($path_plan);
            
            $tagColumeCounter = 0;
            
            $hourId;
            foreach ($planData as $hourValue) {
                
                foreach ($hourValue as $dayValue) {
                    if ($tagColumeCounter != 0) {
                      //  echo "Tage: " . $plan->days[$tagColumeCounter - 1];
                     //   echo "</br>";
                    }
                    $stunde;
                    $fieldTypCounter = 0;
                    
                    foreach ($dayValue as $fildValue) {
                        if ($tagColumeCounter == 0) {
                          //  echo "Stunde: ";
                          //  echo $fildValue;
                          //  echo "</br>";
                            $stunde = $dataFields = preg_replace('/\s+/', '', 
                                    $fildValue);
                        } else {
                            if($fildValue !== ""){
                          //  echo $fildValue . "</br>";
                            $db->insertFieldInfo($hourId, 
                                    preg_replace('/\s+/', ' ', $fildValue), 
                                    $fieldTypCounter);
                            }else{
                              //  echo "Field is empty</br>";
                            }
                        }
                        $fieldTypCounter ++;
                    }
                    $hourResult = $db->insertHour($classResult['id'], 
                            $weekResult['id'], $stunde, $tagColumeCounter);
                    $hourId = $hourResult['id'];
                    $tagColumeCounter ++;
                }
                $tagColumeCounter = 0;
            }
         
        }
    }
    $db->closeDB();
}

getData();
?>
	