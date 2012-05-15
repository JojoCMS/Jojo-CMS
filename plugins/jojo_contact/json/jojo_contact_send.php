<?php

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past

$formID = isset($_POST['form_id']) ? $_POST['form_id'] : '';
$response = Jojo_Plugin_Jojo_contact::sendEnquiry($formID);
$res = json_encode($response);
echo $res;
exit;
