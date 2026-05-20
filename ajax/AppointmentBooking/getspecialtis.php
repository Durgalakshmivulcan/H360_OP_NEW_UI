<?php
require_once("../../config/functions.php");
$id=$_session['doc_id'];

$result = [];



    $get=mysqli_query($conn,"SELECT doc_id, education FROM doctors WHERE doc_id ") or die(mysqli_error($conn));
    while ($res = mysqli_fetch_object($get)) {
        $result[] = $res;
    }


echo json_encode($result);

?>