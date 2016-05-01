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
?>