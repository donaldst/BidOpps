<?php
require_once("connection.php");
    $id= $_POST['id'];
    $subheading = $_POST['subheading'];
    $docTitle=$_POST['docTitle'];
    $datetime2 = date_create_from_format('m#d#Y h a', $_POST['Pdate']);
	$postedDate = date_format($datetime2, 'Y-m-d H:i:s');
    $datetime1 = $_POST['dueDate'];
    $sequence= $datetime1;
    if (isset($_POST['id'])) {
        $query = "UPDATE `opportunity_docs` SET `subheading` = '$subheading', `title`= '$docTitle', `posted_date`='$postedDate'  WHERE `document_id` = '$id'"; //Insert Query
        mysqli_query($bd, $query);
    }

?>