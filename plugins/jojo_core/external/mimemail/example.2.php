<?php
/**
* Filename.......: example.2.php
* Project........: HTML Mime Mail class
* Last Modified..: 15 July 2002
*/
	error_reporting(E_ALL);
	include('htmlMimeMail.php');

/**
* Example of usage. This example shows
* how to use the class with html,
* embedded images, no attachments, but
* using the third argument of setHtml(),
* which will try to automatically find the
* images (though not limited to images),
* and embed them. It will send the mail
* using built in php mail() functionality.
*
* Create the mail object.
* No header argument any more
*/
	$mail = new htmlMimeMail();
	
/**
* Read the image background.gif into
* $background
*/
	$background = $mail->getFile('background.gif');
	
/**
* If sending an html email, then these
* two variables specify the text and
* html versions of the mail. Don't
* have to be named as these are. Just
* make sure the names tie in to the
* $mail->setHtml() call further down.
*/
	$text = $mail->getFile('example.txt');
	$html = $mail->getFile('example.html');
	
/**
* Add the text, html and embedded images.
* Here we're using the third argument of
* setHtml(), which is the path to the
* directory that holds the images. By
* adding this third argument, the class
* will try to find all the images in the
* html, and auto load them in. Not 100%
* accurate, and you MUST enclose your
* image references in quotes, so src="img.jpg"
* and NOT src=img.jpg. Also, where possible,
* duplicates will be avoided.
*/
	$mail->setHtml($html, $text, './');
	
/**
* Sends the message.
*/
	$mail->setFrom('Joe <joe@example.com>');
	$mail->setSubject('Test mail');
	
	$result = $mail->send(array('postmaster@localhost'));
	
	echo $result ? 'Mail sent!' : 'Failed to send mail';
?>