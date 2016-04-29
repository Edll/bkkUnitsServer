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
		
		$nodeValue;
		$days;
		
		foreach ( $dom->getElementsByTagName ( "table" ) as $table ) {
			$nodeValue [] = $table->nodeValue;
		}
		// echo $counter;
		// remove erstes element das ist die ober Table
		unset ( $nodeValue [0] );
		
		// remove montag - freitag
		for($i = 0; $i < 8; $i ++) {
			unset ( $nodeValue [$i] );
		}
		$counter = 0;
		$counterStunden = 0;
		$counterTage = 0;
		
		$woche;
		$tag;
		$stunden;
		
		foreach ( $nodeValue as $value ) {
			// echo $value." ".$counter."\n";
			//echo "stu " . $counterStunden . "\n";
			//echo "cou " . $counter . "\n";
			//echo "tag " . $counterTage . "\n";
			$tag [$counterTage] = $value;
			$stunden [$counterStunden] = $tag;
			$counter ++;
			if ($counter % 7 == 0) {
				$counterStunden ++;
				$counterTage = 0;
			} else {
				$counterTage ++;
			}
		}
		$size = count($stunden);
		for ($i = size; $i < $size-7; $i--) {
			unset($stunden[$i]);
		}
		
		return $stunden;
	}
}

$path = "http://localhost/untis/fileadmin/technik/infoplaene/schueler/frames/navbar.htm";

$classes = new read_classes ();
$array = $classes->read ( $path );
foreach ( $array as $value ) {
	// echo $value;
	// echo "</br>";
}
// echo json_encode($array);

$weeks = new read_weeks ();
$array = $weeks->read ( $path );
foreach ( $array as $value ) {
	// echo $value;
	// echo "</br>";
}
// echo json_encode($array);

$path_plan = "http://localhost/untis/fileadmin/technik/infoplaene/schueler/17/c/c00020.htm";
$plan = new read_plan ();
$array = $plan->read ( $path_plan );
$counter = 0;
$counter2 = 0;
$days = array (
		"Montag",
		"Dienstag",
		"Mittwoch",
		"Donnerstag",
		"Freitag",
		"Samstag",
		"Sonntag" 
);

foreach ( $array as $value ) {
	// echo "Stunden ".$counter%13;
	foreach ( $value as $value2 ) {
		if ($counter2 == 0) {
			echo "Stunde: ";
		} else {
			echo "Tage: " . $days [$counter2 - 1];
		}
		echo $value2;
		echo "</br>";
		$counter2 ++;
	}
	$counter2 = 0;
	$counter ++;
}
?>
	