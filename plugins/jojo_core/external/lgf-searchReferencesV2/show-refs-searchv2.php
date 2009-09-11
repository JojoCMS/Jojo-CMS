<?php
/*
	--------------------------------------------
		LGF Referrer Log Display Page
		By Charles Johnson
		Copyright 2001 LGF Web Design
		All Rights Reserved.
		http://littlegreenfootballs.com

		This file may be freely distributed
		as long as the credits and copyright
		message above remain intact.
	--------------------------------------------
*/

// Name of referrer log file
$reflog = "/PATH/TO/FILE/reflog-searchv2.txt";
include('/PATH/TO/FILE/lgf-search-Functions.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">
<html>
<head>
<title>Last 50 Referrers</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
body {
  background-color: #FFFFFF;
}
p {
  font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
  font-size: 10px;
  line-height: 14px;
  color: #000000;
}
</style>
</head>

<body>
<?php

$rfile = file($reflog);													// read the referrer log into an array
echo "<p>\n";
foreach ($rfile as $r) {     // loop through the array

	$r = chop($r);														// remove trailing whitespace
	if ($r <> "Direct request") {
	
		$keywordsArray = ExtractKeywords3($r);
		$keywords ="";
		if ($keywordsArray != "") {
			foreach ($keywordsArray as $value) {
    			$keywords .=$value." ";
			}
	
			echo "<a href=\"$r\">$keywords</a><br />\n";	// if not a direct request, link it up
		}
	}
}
echo "</p>\n";
?>
</body>
</html>
