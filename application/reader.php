<?php

class read_classes {
	function read($path) {
		$file = file_get_contents ( $path );
		$search_string_start = "var classes = [";
		$search_string_end = " var flcl = 1; var flte = 1;";
		
		$start = strpos ( $file, $search_string_start );
		$start = $start + strlen ( $search_string_start );
		
		$end = strpos ( $file, $search_string_end );
		
		$classes_string = substr ( $file, $start, ($end - $start - 4) );
		$classes_string = str_replace ( "\"", "", $classes_string );
		$classes = explode ( ",", $classes_string );
		
		return $classes;
	}
}

class read_weeks {
	function read($path) {
		$file = file_get_contents ( $path );
		
		$search_string_start = "<select name=\"week\" class=\"selectbox\" onChange=\"doDisplayTimetable(NavBar, topDir);\">";
		$search_string_end = "<select name=\"type\" class=\"selectbox\" onChange=\"ChangeElementOptions(NavBar);\">";
		
		$start = strpos ( $file, $search_string_start );
		$start = $start + strlen ( $search_string_start );
		
		$end = strpos ( $file, $search_string_end );
		
		$week_string = substr ( $file, $start, ($end - $start - 286) );
		
		$week_string = str_replace ( "<", "", $week_string );
		$week_string = str_replace ( ">", "", $week_string );
		$week_string = str_replace ( "option", "", $week_string );
		$week_string = str_replace ( "/", "", $week_string );
		$week_string = str_replace ( "value=", "", $week_string );
		$week_string = str_replace ( "\"", " ", $week_string );
		
		$weeks = explode ( " ", $week_string );
		
		unset ( $weeks [0] );
		unset ( $weeks [1] );
		unset ( $weeks [4] );
		
		return $weeks;
	}
}

class read_plan {
	function read($path) {
		$file = file_get_contents ( $path );
		
		$dom = new DOMDocument ();
		// @ is suppress for html parse failure. The Document is not a W3C file.
		@$dom->loadHTML ( $file );
		
		$days = array (
				"Montag",
				"Dienstag",
				"Mittwoch",
				"Donnerstag",
				"Freitag",
				"Samstag",
				"Sonntag" 
		);
		
		$nodeValue;
		$days;
		$counter=0;
		foreach ( $dom->getElementsByTagName ( "table" ) as $tr ) {
			
			//echo $tr->nodeValue;
			//$tr->nodeValue = $tr->nodeValue.$counter;
			//echo "\n";
			//echo $counter."\n";
			$nodeValue [] = $tr->nodeValue." c: ".$counter;
			$counter++;
			/*
			foreach ( $tr->getElementsByTagName ( "td" ) as $td ) {
				foreach ( $days as $day ) {
					if (strpos ( $td->nodeValue, $day ) !== false) {
						$days [] = $td->nodeValue;
					}
				}
			}
			*/
	
		}
		unset ( $nodeValue [0] );
		foreach ($nodeValue as $value) {
			echo $value."\n";
		}
		/*$counter = 0;
		$nodeValuesDelet;
		foreach ( $nodeValue as $value ) {
			foreach ( $days as $day ) {
				if (strpos ( $value, $day ) !== false) {
					$nodeValuesDelet [] = $counter;
				}
				$counter ++;
			}
		}
		// echo $nodeValuesDelet;
		foreach ( $nodeValuesDelet as $array_key ) {
			unset ( $nodeValue [$array_key] );
		}*/
		return $nodeValue;
	}
}

$path = "http://localhost/untis/fileadmin/technik/infoplaene/schueler/frames/navbar.htm";

$classes = new read_classes ();
$array = $classes->read ( $path );
foreach ( $array as $value ) {
	//echo $value;
	//echo "</br>";
}
// echo json_encode($array);

$weeks = new read_weeks ();
$array = $weeks->read ( $path );
foreach ( $array as $value ) {
	//echo $value;
	//echo "</br>";
}
// echo json_encode($array);

$path_plan = "http://localhost/untis/fileadmin/technik/infoplaene/schueler/17/c/c00073.htm";
$plan = new read_plan ();
$array = $plan->read ( $path_plan );
foreach ( $array as $value ) {
	//echo $value;
	// echo "</br>";
}
?>
	