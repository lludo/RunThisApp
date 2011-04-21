<?php

use Entities\Device;

require_once __DIR__ . '/lib/cfpropertylist/CFPropertyList.php';
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

//TODO: use $_GET['app'] and $_GET['key'] to verify response integrity
//TODO: verify if the device does not already exist (Update data for the same udid if it exists)

$device = new Device;
$device->setName($plistData['DEVICE_NAME']);
$device->setDateCreation(new \DateTime("now"));
$device->setSystemVersion($plistData['VERSION']);
$device->setModel($plistData['PRODUCT']);
$device->setUdid($plistData['UDID']);
//$device->setInvitation($invitation);
$device->setTester($tester);

$entityManager->persist($device);
$entityManager->flush();

?>