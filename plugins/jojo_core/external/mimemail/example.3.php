<?php
/**
* Filename.......: example.3.php
* Project........: HTML Mime Mail class
* Last Modified..: 15 July 2002
*/
	error_reporting(E_ALL);
	include('htmlMimeMail.php');

/**
* Example of usage. This example shows
* how to use the class to send a plain
* text email with an attachment. No html,
* or embedded images.
*
* Create the mail object.
*/
	$mail = new htmlMimeMail();
	
/**
* Read the file test.zip into $attachment.
*/
	$attachment = $mail->getFile('example.zip');

/**
* Since we're sending a plain text email,
* we only need to read in the text file.
*/
	$text = $mail->getFile('example.txt');

/**
* To set the text body of the email, we
* are using the setText() function. This
* is an alternative to the setHtml() function
* which would obviously be inappropriate here.
*/	
	$mail->setText($text);

/**
* This is used to add an attachment to
* the email.
*/
	$mail->addAttachment($attachment, 'example.zip', 'application/zip');

/**
* Sends the message.
*/
	$mail->setFrom('Joe <joe@example.com>');
	$result = $mail->send(array('"Richard" <postmaster@localhost>'));
	
	echo $result ? 'Mail sent!' : 'Failed to send mail';
?>