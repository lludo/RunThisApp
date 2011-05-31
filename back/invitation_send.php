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
require_once __DIR__ . '/../mail.php';
require_once __DIR__ . '/../core/functions.php';
require_once __DIR__ . '/../tools.php';
require_once __DIR__ . '/../lib/Swift/lib/swift_required.php';

$entityManager = initDoctrine();
date_default_timezone_set('Europe/Paris');

//send the invits
$nbInvitations = 0;
$sendMailError = "";


if ( $_POST['selected_device_new'] ) {
    
    if ( $_POST['selected_tester'] == 'new_tester' ) {
        //create new tester : new_tester_email
        //create new empty device : new_tester_email(mail)
    } else {
        //create new empty device : value(mail)
    }
}

$versionId = $_POST['selected_version'];
$version = $entityManager->getRepository('Entities\Version')->find($versionId);
$application = $version->getApplication();
$msg = $_POST['body'];

$smtp = Swift_SmtpTransport::newInstance($CRED_SMTP, $CRED_SMTP_PORT, 'ssl')
->setUsername($CRED_SMTP_USR)
->setPassword($CRED_SMTP_PWD);
$mailer = Swift_Mailer::newInstance($smtp);

$appBundleId = $application->getBundleId();
$app = $application->getName();
$ver = $version->getVersion();
$url = Tools::rel2abs('../runthisapp.php', Tools::current_url());
foreach ($_POST['selected_devices'] as $deviceId) {
	$device = $entityManager->getRepository('Entities\Device')->find($deviceId);
	$udid = $device->getUdid();
    $mail = $device->getTester()->getEmail();
    $token = Tools::randomAppleRequestId();
	
	if (empty($udid)) {
		$result = send_enroll_mail($mailer, $url, $app, $ver, $msg, $mail, $token);
	} else {
		$result = send_link_mail($mailer, $url, $app, $ver, $msg, $mail, $udid, $token);
	}
    
    // Ok
    if ( $result == 1 ) {
        $nbInvitations++;
        
        $tester = $entityManager->getRepository('Entities\Tester')->findOneBy(array('email' => $email));
        
        //TODO: if tester does not exist, create it.
        
        $invitation = new Invitation();
        $invitation->setSubject("//TODO delete me");
        $invitation->setText($msg);
        $invitation->setToken($token);
        $invitation->setDateSent(new \DateTime("now"));
        $invitation->setStatus(Invitation::STATUS_SENT);
        //$invitation->setDeveloper(//TODO:)
        $invitation->SetTester($tester);
        $invitation->SetVersion($version);
        $entityManager->persist($invitation);
        
        //TODO: add application to invitation object in model !!!
    }
    // Error
    else {
        $sendMailError += 'The invitation was not sent to: ' . $email . '</br>';
    }
}

$entityManager->flush();

?>
<!doctype html>
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