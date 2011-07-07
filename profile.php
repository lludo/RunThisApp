<?php

use Entities\Device;
use Entities\Tester;
use Entities\Invitation;

require_once __DIR__ . '/lib/cfpropertylist/CFPropertyList.php';
require_once __DIR__ . '/lib/Swift/lib/swift_required.php';
require_once __DIR__ . '/credentials.php';
require_once __DIR__ . '/core/index.php';
require_once __DIR__ . '/tools.php';
require_once __DIR__ . '/mail.php';
require_once __DIR__ . '/asn.php';

$ciphertext_file = tempnam('', '__glancemyapp_');
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
/*
 * TEST: $plistValue = '<?xml version="1.0" encoding="UTF-8"?><!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd"><plist version="1.0"><dict><key>CHALLENGE</key><string>signed-auth-token</string><key>IMEI</key><string>00 000000 000000 0</string><key>PRODUCT</key><string>iPhone3,1</string><key>UDID</key><string>d2f22d586c333041d191c81e5bb80948732d6a68</string><key>VERSION</key><string>9A5248d</string></dict></plist>';
 */

if (empty($plistValue)) {
    error_log('RTA::Unable to read plist from configuration profile challenge', 0);
    die();
}

$plist = new CFPropertyList();
$plist->parse( $plistValue, CFPropertyList::FORMAT_AUTO);
$plistData = $plist->toArray();

$entityManager = initDoctrine();
date_default_timezone_set('Europe/Paris');

$invitation = $entityManager->getRepository('Entities\Invitation')->findOneBy(array('token' => $_GET['key']));
if ( $invitation == NULL ) {
    error_log('RTA::This invitation is not valid!', 0);
    die();
}

$tester = $invitation->getTester();
if ( $tester == NULL ) {
    error_log('RTA::This user does not exist!', 0);
    die();
}

//Verify if the device does not already exist (Update data for this device if it exists)
$device = $invitation->getDevice();
if ( $device == NULL ) {
    $device = new Device;
}

$device->setName($plistData['DEVICE_NAME']);
$device->setDateCreation(new \DateTime("now"));
$device->setSystemVersion($plistData['VERSION']);
$device->setModel($plistData['PRODUCT']);
$device->setUdid($plistData['UDID']);
//TODO: see why memory problem if uncoment $device->setInvitation($invitation);
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

$url = Tools::rel2abs('runthisapp.php', Tools::current_url());
$msg = 'Click on following link to install your app: ';

$udid = $device->getUdid();
$mail = $device->getTester()->getEmail();
$token = $_GET['key'];

$app = $invitation->getVersion()->getApplication()->getName();
$ver = $invitation->getVersion()->getVersion();

$result = send_link_mail($mailer, $url, $app, $ver, $msg, $mail, $udid, $token);

// Error
if ( $result != 1 ) {
    error_log('RTA::Error sending email', 0);
    die();
}

?>
