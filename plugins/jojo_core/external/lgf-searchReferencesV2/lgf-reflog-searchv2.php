<?php
/*
	--------------------------------------------
		LGF Referrer Log
		By Charles F. Johnson
		Copyright 2001 LGF Web Design
		All Rights Reserved.
		http://littlegreenfootballs.com

		This file may be freely distributed
		as long as the credits and copyright
		message above remain intact.
	--------------------------------------------
*/

/*
Extract Keywords from MT-Refsearch by
http://eliot.landrum.cx/archives/2002/12/12/07_the_wonderwhammy_release.php

additional hacks by jennifer @ scriptygoddess.com

*/

// Name of referrer log file
$reflog = '/PATH/TO/FILE/reflog-searchv2.txt';

// Name of semaphore file
$semaphore = '/PATH/TO/FILE/semaphore-search.ref';

// Maximum number of referrers to log
$maxref = 50;

// Domain name of this site (minus "http://www.")
$domain = 'YOURDOMAIN.COM';

// From whence did Bunky come?
$refer = getenv("HTTP_REFERER");

include('/PATH/TO/FILE/lgf-search-Functions.php');
// Cover me. I'm going in.
 
$keywordsArray = ExtractKeywords3($refer);
if ($keywordsArray != "") {
	$refer .= "\n";								// append a line feed
	$sp = fopen($semaphore, "w");				// open the semaphore file
	if (flock($sp, 2)) {						// lock the semaphore; other processes will stop and wait here
		$rfile2 = file($reflog);					// read the referrer log into an array
		if ($refer <> $rfile2[0]) {				// if this referrer is different from the last one
			if (count($rfile2) == $maxref)		// if the file is full
				array_pop($rfile2);				// pop the last element
			array_unshift($rfile2, $refer);		// push the new referrer onto the front
			$r = join("", $rfile2);				// make the array into a string
			$rp = fopen($reflog, "w");			// open the referrer log in write mode
			$status = fwrite($rp, $r);			// write out the referrer URLs
			$status = fclose($rp);				// close the log
		}
	}
	$status = fclose($sp);						// close the semaphore (and release the lock)
}


?>