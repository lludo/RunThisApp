<?php

use Entities\Device;
use Entities\Tester;
use Entities\Invitation;

require_once __DIR__ . '/lib/cfpropertylist/CFPropertyList.php';
require_once __DIR__ . '/lib/Swift/lib/swift_required.php';
require_once __DIR__ . '/credentials.php';
require_once __DIR__ . '/lib/log.php';
require_once __DIR__ . '/core/index.php';
require_once __DIR__ . '/asn.php';


$lf = new logfile();

$ciphertext_file   = tempnam('', '__glancemyapp_');
file_put_contents($ciphertext_file, file_get_contents('php://input'));
/*
OpenSSL PHP implementation does'nt allow use to verify DER encoded CMS. :/
This works well in command line: openssl.exe smime -verify -noverify -in "payload.bin" -inform DER
but not using openssl_pkcs7_verify...

$certificates_file = tempnam('', '__glancemyapp_');
$deciphertext_file   = tempnam('', '__glancemyapp_');
$result = openssl_pkcs7_verify(
		$ciphertext_file,
		PKCS7_BINARY | PKCS7_TEXT | PKCS7_NOVERIFY | PKCS7_NOCHAIN,
		$certificates_file,
		array(),
		null,
		$deciphertext_file
);
unlink($ciphertext_file);
unlink($certificates_file);
$plist = file_get_contents($deciphertext_file);
unlink($deciphertext_file);
while (($e = openssl_error_string()) !== false) {
	$lf->write('openssl error='.$e.PHP_EOL);
}
*/
$plistValue = retreivePlistFromAsn($ciphertext_file);
if (empty($plistValue)) {
	die("unable to read plist from configuration profile challenge.");
}

$plist = new CFPropertyList();
$plist->parse( $plistValue, CFPropertyList::FORMAT_AUTO);

//echo '<pre>';
//var_dump( $plist->toArray() );
$plistData = $plist->toArray();

//$lf->writeln('UDID='.$plistData['UDID']);
//$lf->writeln('mail='.$_GET['mail']);
//$lf->writeln('app='.$_GET['app']);
//$lf->writeln('key='.$_GET['key']);
//echo '</pre>';

$entityManager = initDoctrine();
date_default_timezone_set('Europe/Paris');

// Retrieve the tester with his mail (unique)
$tester = $entityManager->getRepository('Entities\Tester')->findOneBy(array('email' => $_GET['mail']));
if ( $tester == NULL ) {
    die('This user does not exist!');
}

//TODO: add application check to retrieve invitation
//TODO: use $_GET['app'] and $_GET['key'] to verify response integrity
$invitation = $entityManager->getRepository('Entities\Invitation')->findOneBy(array('token' => $_GET['key']));
if ( $invitation == NULL ) {
    die('There is no valid invitation for this user!');
}

//Verify if the device does not already exist (Update data for this device if it exists)
$device = $entityManager->getRepository('Entities\Device')->findOneBy(array('udid' => $plistData['UDID']));

if ( $device == NULL ) {
    $device = new Device;
}

$device->setName($plistData['DEVICE_NAME']);
$device->setDateCreation(new \DateTime("now"));
$device->setSystemVersion($plistData['VERSION']);
$device->setModel($plistData['PRODUCT']);
$device->setUdid($plistData['UDID']);
$device->setInvitation($invitation);
$device->setTester($tester);

$invitation->setStatus(Invitation::STATUS_UDID);

$entityManager->persist($device);
$entityManager->persist($invitation);
$entityManager->flush();


//Send the confirmation email

$smtp = Swift_SmtpTransport::newInstance($CRED_SMTP, $CRED_SMTP_PORT, 'ssl')
->setUsername($CRED_SMTP_USR)
->setPassword($CRED_SMTP_PWD);

$mailer = Swift_Mailer::newInstance($smtp);
$body = 'Click on following link to install your app: ';

//TODO: retrieve the app and version

//TODO: use the function service_address() like in enroll.php to have a valid address everytime

$url = 'http://192.168.1.103/rta/runthisapp.php?udid=' . $device->getUdid() . '&app=' . '1234' . '&ver=' . '5678';

$subject = '[2/2] RunThisApp invitation to test finish';
$bodyHtml = $body . '<a href="' . $url . '">' . $url . '</a>';
$bodyText = $body . $url;

//Create the message
$message = Swift_Message::newInstance()
//Give the message a subject
->setSubject($subject)
 //Set the From address with an associative array
->setFrom(array($CRED_SMTP_USR => 'RunThisApp'))
//Set the To addresses with an associative array
->setTo(array($_GET['mail']))
//Give it a body
->setBody($bodyHtml, 'text/html')
//And optionally an alternative body
->addPart($bodyText, 'text/plain');

$result = $mailer->send($message);

// Error
if ( $result != 1 ) {
    die('Error sending email');
}

?>
