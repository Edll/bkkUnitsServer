<?php
include ('db.php');
include ('classes_import.php');
include ('weeks_import.php');
include ('plan_import.php');

define("PATH_NAVI", 
        "http://www.bkkleve.de/fileadmin/technik/infoplaene/schueler/frames/navbar.htm");
define("PATH_PLAN", 
        "http://www.bkkleve.de/fileadmin/technik/infoplaene/schueler/");

ini_set('display_errors', 1);

$read = new reader();
$read->read(TRUE, TRUE);

class reader {

    public $msg;

    function read (bool $showOutput, bool $sendMail) {
        set_error_handler(array(
                $this,
                'warning_handler'
        ), E_ALL);
        
        $this->add_line_to_msg("Start: " . $this->get_time());
        
        try {
            $this->getData();
        } catch (Exception $e) {
            $this->add_line_to_msg('Exception abgefangen: ' . $e->getMessage());
        }
        
        $this->add_line_to_msg("Ende: " . $this->get_time());
        
        if ($showOutput) {
            echo nl2br($this->msg);
        }
        
        if ($sendMail) {
            mail('junk@edlly.de', 'Reader durchgelaufen', $this->msg);
        }
    }

    function getData () {
        $this->add_line_to_msg("Lese NaviBar; " . PATH_NAVI);
        
        $db = new db();
        $db->connectDB();
        
        $weeks = new read_weeks();
        $classes = new read_classes();
        $plan = new read_plan();
        
        foreach ($weeks->read(PATH_NAVI) as $WeekValue) {
            $weekResult = $db->insertWeeks($WeekValue[0], $WeekValue[1]);
            $this->add_line_to_msg(
                    "Eintragen der Woche: " . $weekResult['number']);
            $this->add_line_to_msg("");
            
            $classCounter = 0;
            
            foreach ($classes->read(PATH_NAVI) as $classData) {
                $classCounter ++;
                $classNumber;
                
                if ($classCounter < 10) {
                    $classNumber = "0" . $classCounter;
                } else {
                    $classNumber = $classCounter;
                }
                
                $classResult = $db->insertClass($weekResult['id'], $classNumber, 
                        $classData);
                
                $this->add_line_to_msg(
                        "Eintragen der Klasse: " . $classResult['id'] . " " .
                                 $classResult['name']);
                
                $path_plan = PATH_PLAN . $weekResult['number'] . "/c/c000" .
                         $classNumber . ".htm";
                
                $this->add_line_to_msg("Pfad: " . $path_plan);
                $this->add_line_to_msg("");
                
                $planData = $plan->read($path_plan);
                
                $tagColumeCounter = 0;
                
                foreach ($planData as $hourValue) {
                    
                    foreach ($hourValue as $dayValue) {
                        if ($tagColumeCounter != 0) {}
                        $stunde;
                        $fieldTypCounter = 0;
                        
                        foreach ($dayValue as $fildValue) {
                            if ($tagColumeCounter == 0) {
                                $stunde = $dataFields = preg_replace('/\s+/', 
                                        '', $fildValue);
                            } else {
                                if ($fildValue !== "") {
                                    $db->insertFieldInfo(
                                            preg_replace('/\s+/', ' ', 
                                                    $fildValue), $fieldTypCounter, 
                                            $weekResult['id'], $stunde, 
                                            $classResult['id'], 
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
    }

    private function get_time () {
        $datum = date("d.m.Y");
        $uhrzeit = date("H:i:s");
        return $datum . " - " . $uhrzeit . " Uhr";
    }

    private function add_line_to_msg ($msg) {
        $this->msg = $this->msg . $msg . " \n ";
    }

    function warning_handler ($errno, $errstr) {
        $this->add_line_to_msg(
                $this->FriendlyErrorType($errno) . " : " . $this->get_time() .
                         " : " . $errstr);
    }

    private function FriendlyErrorType ($type) {
        switch ($type) {
            case E_ERROR: // 1 //
                return 'E_ERROR';
            case E_WARNING: // 2 //
                return 'E_WARNING';
            case E_PARSE: // 4 //
                return 'E_PARSE';
            case E_NOTICE: // 8 //
                return 'E_NOTICE';
            case E_CORE_ERROR: // 16 //
                return 'E_CORE_ERROR';
            case E_CORE_WARNING: // 32 //
                return 'E_CORE_WARNING';
            case E_COMPILE_ERROR: // 64 //
                return 'E_COMPILE_ERROR';
            case E_COMPILE_WARNING: // 128 //
                return 'E_COMPILE_WARNING';
            case E_USER_ERROR: // 256 //
                return 'E_USER_ERROR';
            case E_USER_WARNING: // 512 //
                return 'E_USER_WARNING';
            case E_USER_NOTICE: // 1024 //
                return 'E_USER_NOTICE';
            case E_STRICT: // 2048 //
                return 'E_STRICT';
            case E_RECOVERABLE_ERROR: // 4096 //
                return 'E_RECOVERABLE_ERROR';
            case E_DEPRECATED: // 8192 //
                return 'E_DEPRECATED';
            case E_USER_DEPRECATED: // 16384 //
                return 'E_USER_DEPRECATED';
        }
        return "";
    }
}
?>
	