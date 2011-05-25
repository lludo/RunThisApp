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
require_once __DIR__ . '/../core/functions.php';
require_once '/../lib/swift/lib/swift_required.php';

$entityManager = initDoctrine();


//send the invits
$nbInvitations = 0;
$versionId = $_POST['selected_version'];
$body = $_POST['body'];
$body .= PHP_EOL . "Click on this link to install it: " ;
$url = "http://runthisapp.com";
$bodyHtml = $body . '<a href="' . $url . '">' . $url . '</a>';
$bodyText = $body . $url;
$version = $entityManager->getRepository('Entities\Version')->find($versionId);
$application = $version->getApplication();

$smtp = Swift_SmtpTransport::newInstance($CRED_SMTP, $CRED_SMTP_PORT, 'ssl')
->setUsername($CRED_SMTP_USR)
->setPassword($CRED_SMTP_PWD);
 
$mailer = Swift_Mailer::newInstance($smtp);
//print_r($_POST);
foreach ($_POST['selected_devices'] as $deviceId) {
  $device = $entityManager->getRepository('Entities\Device')->find($deviceId);
  $email = $device->getTester()->getEmail();
  
  
  //Create the message
  $message = Swift_Message::newInstance()
  //Give the message a subject
  ->setSubject('RunThisApp invitation to test '.$application->getName().' v'.$version->getVersion())
  //Set the From address with an associative array
  ->setFrom(array($CRED_SMTP_USR => 'RunThisApp'))
  //Set the To addresses with an associative array
  ->setTo(array($email))
  //Give it a body
  ->setBody($bodyHtml, 'text/html')
  //And optionally an alternative body
  ->addPart($bodyText, 'text/plain')
;  
  $result = $mailer->send($message);
	//TODO check result
  $nbInvitations++;
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
	
	<p>
	<?php echo $nbInvitations; ?> invitations sent.
	</p>

	</body>
</html>