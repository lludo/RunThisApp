<?php 

/*
 *    RunThisApp allows sharing test builds of iOS apps with testers.
 *    Copyright (C) 2011 Ludovic Landry & Pascal Cans
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

function send_enroll_mail($mailer, $url, $app, $ver, $msg, $mail, $token) {

    $body = $msg . PHP_EOL . "Click on following link to get started: " ;
    $url .= '?token=' . $token;
	$subject = '[Preliminary Step]';
	$subject .= ' RunThisApp invitation to test '. $app .' v'.$ver;
    
	return send_mail($mailer, $body, $url, $subject, $mail);
}

function send_link_mail($mailer, $url, $app, $ver, $msg, $mail, $udid, $token) {
	$body = $msg . PHP_EOL . "Click on following link to get started: " ;
    $url .= '?token=' . $token . '&udid=' . $udid;
	$subject = 'RunThisApp invitation to test '. $app .' v'.$ver;
    return send_mail($mailer, $body, $url, $subject, $mail);
}

function send_mail($mailer, $body, $url, $subject, $mail) {

	$bodyHtml = $body . '<a href="' . $url . '">' . $url . '</a>';
    $bodyText = $body . $url;
	//Create the message
    $message = Swift_Message::newInstance()
    //Give the message a subject
    ->setSubject($subject)
    //Set the From address with an associative array
    ->setFrom(array('RunThisApp@' . $_SERVER['SERVER_NAME'] => 'RunThisApp'))
    //Set the To addresses with an associative array
    ->setTo(array($mail))
    //Give it a body
    ->setBody($bodyHtml, 'text/html')
    //And optionally an alternative body
    ->addPart($bodyText, 'text/plain');
    
    return $mailer->send($message);
}

?>