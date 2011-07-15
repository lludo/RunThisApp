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

$versionId = $_POST['selected_version'];
$version = $entityManager->getRepository('Entities\Version')->find($versionId);
$msg = $_POST['body'];

$smtp = Swift_SmtpTransport::newInstance($CRED_SMTP, $CRED_SMTP_PORT, 'ssl')
->setUsername($CRED_SMTP_USR)
->setPassword($CRED_SMTP_PWD);
$mailer = Swift_Mailer::newInstance($smtp);

$url = Tools::rel2abs('../runthisapp.php', Tools::current_url());

function sendInvitationForDevice($device, $mailer, $url, $version, $msg, $entityManager) {
	
	$udid = $device->getUdid();
	$mail = $device->getTester()->getEmail();
	$token = Tools::randomAppleRequestId();

	$app = $version->getApplication()->getName();
	$ver = $version->getVersion();
        
	if (empty($udid)) {
		$result = send_enroll_mail($mailer, $url, $app, $ver, $msg, $mail, $token);
	} else {
		$result = send_link_mail($mailer, $url, $app, $ver, $msg, $mail, $udid, $token);
	}
	
	// Ok
	if ( $result == 1 ) {
	    $nbInvitations++;
	    
	    //TODO: if tester does not exist, create it.
	    
	    $invitation = new Invitation();
	    $invitation->setSubject("Mail subject");
	    $invitation->setText($msg);
	    $invitation->setToken($token);
	    $invitation->setDateSent(new \DateTime("now"));
	    $invitation->setStatus(Invitation::STATUS_SENT);
	    //$invitation->setDeveloper(//TODO:)
	    $invitation->setTester($device->getTester());
		$invitation->setDevice($device);
	    $invitation->setVersion($version);
	    $entityManager->persist($device);
	    $entityManager->persist($invitation);
	    $entityManager->flush();
	}
	// Error
	else {
	    $sendMailError += 'The invitation was not sent to: ' . $email . '</br>';
	}
}

if ( $_POST['selected_device_new'] ) {
    
    if ( $_POST['selected_tester'] == 'new_tester' ) {
        //create new tester : new_tester_email
        //create new empty device : new_tester_email(mail)
    } else {
        //create new empty device : value(mail)
        
        $tester = $entityManager->getRepository('Entities\Tester')->findOneBy(array('email' => $_POST['selected_tester']));
        
        $device = new Device();
        $device->setTester($tester);
        $entityManager->persist($device);
        $entityManager->flush();
        
        sendInvitationForDevice($device, $mailer, $url, $version, $msg, $entityManager);
    }
}

if (isset($_POST['selected_devices'])) {
	foreach ($_POST['selected_devices'] as $deviceId) {
		$device = $entityManager->getRepository('Entities\Device')->find($deviceId);
		//echo var_dump($device);
		sendInvitationForDevice($device, $mailer, $url, $version, $msg, $entityManager);
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