<?php
/**
* Filename.......: example.5.php
* Project........: HTML Mime Mail class
* Last Modified..: 15 July 2002
*/

        error_reporting(E_ALL);
        include('htmlMimeMail.php');

/**
* Example of usage. This example shows
* how to use the class to send an email
* attached to another email. First email
* built is html/text with an embedded image
* and attachment. This is then attached
* to the second email which is plain text.
*
* Create the mail object.
*/
	$mail_1 = new htmlMimeMail();

/**
* This call is usually not necessary unless you're sending the final
* mail via SMTP (Qmail won't send mail with bare LFs and you must
* therefore use CRLF), but setting this explicitly doesnt hurt.
*/
	$mail_1->setCrlf("\n");

/**
* First email.
*/
	$mail_1->setHTML($mail_1->getFile('example.html'), $mail_1->getFile('example.txt'), dirname(__FILE__) . '/');
	
/**
* Add the attachment
*/
	$mail_1->addAttachment($mail_1->getFile('example.zip'), 'example.zip', 'application/zip');

/**
* Don't send this email, but use the
* get_rfc822() method to assign it to a
* variable.
*/
	$mail_1->setReturnPath('return@example.com');
	$mail_1->setFrom('John Doe <john.doe@example.com>');
	$mail_1->setSubject('Test attached email');
	$mail = $mail_1->getRFC822(array('Nobody <nobody@example.com>'));

/**
* Now start a new mail, and add the first
* (which is now built and contained in
* $mail) to it.
*/
	$mail_2 = new htmlMimeMail();

	$mail_2->setText('This email has an attached email');
	$mail_2->addAttachment($mail, 'Test for attached email', 'message/rfc822', '7bit');

	$mail_2->setFrom('Foo <foo@example.com>');	
	$mail_2->setSubject('Test with attached email');
	$result = $mail_2->send(array('postmaster@localhost'));
	
	echo $result ? 'Mail sent!' : 'Failed to send mail';
?>