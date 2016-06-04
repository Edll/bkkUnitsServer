<?php
include ('db.php');

$db = new db();
$db->connectDB();

// echo var_dump($_GET);

// Todo Has Key check!
if ($_GET["class"] !== null && $_GET["class"] == "all") {
    $result = $db->selectClass(null, null, null, null);
    while ($row = mysqli_fetch_array($result)) {
        echo json_encode($row);
    }
}else if ($_GET["class"] !== null) {
    $result = $db->selectClass(null, null, null, $_GET["class"]);
    while ($row = mysqli_fetch_array($result)) {
        echo json_encode($row);
    }
}else if($_GET["week"] == "all"){
    $result = $db->selectWeek(null, null, null);
    while ($row = mysqli_fetch_array($result)) {
        echo json_encode($row);
    }
}else if($_GET["week"] !== null){
    $result = $db->selectWeek(null, $_GET["week"], null);
    while ($row = mysqli_fetch_array($result)) {
        echo json_encode($row);
    }
}else if($_GET["field"] == "all"){
    $result = $db->selectFieldInfo(null, null, null, null, null);
    while ($row = mysqli_fetch_array($result)) {
        echo json_encode($row);
    }
}else if($_GET["field"] !== null){
    $result = $db->selectFieldInfo(null, null, $_GET["field"], null, null);
    while ($row = mysqli_fetch_array($result)) {
        echo json_encode($row);
    }
}
?>