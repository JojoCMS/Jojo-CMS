<?php
/**
* Filename.......: example.1.php
* Project........: HTML Mime Mail class
* Last Modified..: 15 July 2002
*/
        error_reporting(E_ALL);
        include('htmlMimeMail.php');

/**
* Example of usage. This example shows
* how to use the class with html,
* embedded images, and an attachment.
*/
        /**
        * Create the mail object.
		* No longer takes any arguments
        */
        $mail = new htmlMimeMail();

		/*
        * Read the image background.gif into
		* $background
        */
        $background = $mail->getFile('background.gif');

        /*
        * Read the file test.zip into $attachment.
        */
        $attachment = $mail->getFile('example.zip');

        /*
        * Get the contents of the example text/html files.
		* Text/html data doesn't have to come from files,
		* could come from anywhere.
        */
        $text = $mail->getFile('example.txt');
        $html = $mail->getFile('example.html');

        /*
        * Add the text, html and embedded images.
        * The name (background.gif in this case)
		* of the image should match exactly
        * (case-sensitive) to the name in the html.
        */
        $mail->setHtml($html, $text);
        $mail->addHtmlImage($background, 'background.gif', 'image/gif');

        /*
        * This is used to add an attachment to
        * the email. Due to above, the $attachment
		* variable contains the example zip file.
        */
        $mail->addAttachment($attachment, 'example.zip', 'application/zip');

        /*
        * Set the return path of the message
        */
		$mail->setReturnPath('joe@example.com');
		
		/**
        * Set some headers
        */
		$mail->setFrom('"Joe" <joe@example.com>');
		$mail->setSubject('Test mail');
		$mail->setHeader('X-Mailer', 'HTML Mime mail class (http://www.phpguru.org)');
		
		/**
        * Send it using SMTP. If you're using Windows you should *always* use
		* the smtp method of sending, as the mail() function is buggy.
        */
		$result = $mail->send(array('postmaster@localhost'), 'smtp');

		// These errors are only set if you're using SMTP to send the message
		if (!$result) {
			print_r($mail->errors);
		} else {
			echo 'Mail sent!';
		}
?>