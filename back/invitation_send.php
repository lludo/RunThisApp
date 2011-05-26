<?php

date_default_timezone_set('Europe/Berlin');

use Entities\Application, 
	Entities\Developer,
	Entities\Device,
	Entities\Invitation,
	Entities\Tester,
	Entities\Version;

require_once __DIR__ . '/../credentials.php';
require_once __DIR__ . '/../core/index.php';
require_once __DIR__ . '/../core/tools.php';
require_once __DIR__ . '/../core/functions.php';
require_once __DIR__ . '/../lib/Swift/lib/swift_required.php';

$entityManager = initDoctrine();
date_default_timezone_set('Europe/Paris');

//send the invits
$nbInvitations = 0;
$sendMailError = "";

$versionId = $_POST['selected_version'];
$version = $entityManager->getRepository('Entities\Version')->find($versionId);
$application = $version->getApplication();

$smtp = Swift_SmtpTransport::newInstance($CRED_SMTP, $CRED_SMTP_PORT, 'ssl')
->setUsername($CRED_SMTP_USR)
->setPassword($CRED_SMTP_PWD);

$mailer = Swift_Mailer::newInstance($smtp);
$body = $_POST['body'];

foreach ($_POST['selected_devices'] as $deviceId) {
    
    $device = $entityManager->getRepository('Entities\Device')->find($deviceId);
    
    $email = $device->getTester()->getEmail();
    $appBundleId = $application->getBundleId();
    $key = Tools::randomAppleRequestId();
    
    $body .= "Click on following link to get started: " ;
    
    //$url = "http://runthisapp.com/enroll.php?mail=X&app=X&key=X";
    $url = 'http://192.168.1.103/rta/enroll.php?mail=' . $email . '&app=' . $appBundleId . 'key=' . $key;
    
    $subject = 'RunThisApp invitation to test '. $application->getName() .' v'.$version->getVersion();
    $bodyHtml = $body . '<a href="' . $url . '">' . $url . '</a>';
    $bodyText = $body . $url;
    
    //Create the message
    $message = Swift_Message::newInstance()
    //Give the message a subject
    ->setSubject($subject)
    //Set the From address with an associative array
    ->setFrom(array($CRED_SMTP_USR => 'RunThisApp'))
    //Set the To addresses with an associative array
    ->setTo(array($email))
    //Give it a body
    ->setBody($bodyHtml, 'text/html')
    //And optionally an alternative body
    ->addPart($bodyText, 'text/plain');
    
    $result = $mailer->send($message);
    
    // Ok
    if ( $result == 1 ) {
        $nbInvitations++;
        
        $tester = $entityManager->getRepository('Entities\Tester')->findOneBy(array('email' => $email));
        
        //TODO: if tester does not exist, create it.
        
        $invitation = new Invitation();
        $invitation->setSubject($subject);
        $invitation->setText($bodyHtml);
        $invitation->setToken($key);
        $invitation->setDateSent(new \DateTime("now"));
        $invitation->setStatus(Invitation::STATUS_SENT);
        //$invitation->setDeveloper(//TODO:)
        $invitation->SetTester($tester);
        $entityManager->persist($invitation);
        
        //TODO: add application to invitation object in model !!!
    }
    // Error
    else {
        $sendMailError += 'The invitation was not sent to: ' . $email . '</br>';
    }
    
    $entityManager->flush();
}



?><!doctype html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Run This App | Send Invitation</title>
	<link href="../css/style-0001.css" media="screen" type="text/css" rel="stylesheet">
	
<style>
</style>
</head>
<body>

	<div id="header">
		<h2><a href="../">Run This App</a></h2>
		<ul class="menu">
			<li class="invitations"><a href="invitation_list.php">Invitations</a></li>
			<li class="testers active">Testers</li>
        	<li class="applications"><a href="application_list.php">Applications</a></li>
		</ul>
		
	</div>
	
        <p id="error" ><?php echo $sendMailError; ?></p>
	<p><?php echo $nbInvitations; ?> invitations sent.</p>

	</body>
</html>