<?php

class read_plan {

    public $days = array(
            "Montag",
            "Dienstag",
            "Mittwoch",
            "Donnerstag",
            "Freitag",
            "Samstag",
            "Sonntag"
    );

    function read ($path) {
        $file = file_get_contents($path);
        $stundenArray;
        $dom = new DOMDocument();
        // @ is suppress for html parse failure. The Document is not a W3C file.
        @$dom->loadHTML($file);
        
        $nodeValue;
        $days;
        
        $nodeList = new DOMNodeList();
        
        $nodeList = $dom->getElementsByTagName("table");
        // Abfrage ob das DOM Model Standartplan ist.
        if ($nodeList->length != 0) {
            foreach ($nodeList as $table) {
                $nodeValue[] = $table;
            }
            // remove erstes element das ist die ober Table
            unset($nodeValue[0]);
            
            // remove montag - freitag
            for ($i = 0; $i < 7; $i ++) {
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
                if ($dataCounter % 6 == 0) {
                    $stundeCounter ++;
                    $tagCounter = 0;
                } else {
                    $tagCounter ++;
                }
            }
            $size = count($stundenArray);
            
            // entfernen der 14 stunden. wo die her kommt ist unklar. Offenbar
            // aus
            // dem DOM model.
            unset($stundenArray[$size - 1]);
        } else {
            // Kein standart plan
            $tagArray;
            
            $fieldInfoArray;
            
            $fieldInfoArray[0] = "";
            $tagArray[0] = $fieldInfoArray;
            $stundenArray[0] = $tagArray;
            
            // hat die seite eine Information?
            $nodeListH4 = $dom->getElementsByTagName("h4");
            
            if ($nodeListH4->length != 0) {
                foreach ($nodeListH4 as $table) {
                    $nodeValue[] = $table;
                    foreach ($nodeValue as $value) {
                        $fieldInfoArray[0] = $value->nodeValue;
                    }
                }
            } else {
                $fieldInfoArray[0] = "keine Daten";
            }
            
            $tagArray[0] = $fieldInfoArray;
            $tagArray[1] = $fieldInfoArray;
            $stundenArray[1] = $tagArray;
        }
        return $stundenArray;
    }
}
?>