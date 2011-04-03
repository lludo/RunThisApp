<?php

require_once(dirname(__FILE__).'/lib/cfpropertylist/CFPropertyList.php');
require_once(dirname(__FILE__).'/lib/log.php');



function retreivePlistFromAsn($ciphertext_file)
{

	//Title: Simple PHP BER/DER/ASN.1 basic decoder
	//Author: Jean-Luc Cyr
	//Date: 2008-02-13
	//Desc: Simple BER Printable string dumper
	//	read BER data from a file
	 
	// Set input filename
	$filename = 'd:\payload.bin';
	// Set max number of tag to parse (0 = no limit)
	$limit = 0;
	 
	//Open data file
	$f = fopen($filename,'rb');
	//Set read tag number to 0
	$c=0;
	//While not end of file
	while(!feof($f) && empty($plist))
	{
		//Read first block data type
		$type = ord(fread($f,1));
		if ($type==0)
		  break;
		//Read first len block
		$len = ord(fread($f,1));
		if ($len==0)
		  break;
		$le = 0;
		//If first bit of len is set (1)
		//we have a multiple bit len to read
		//echo "Read Len ".($len)." Bytes\r\n";
		if (($len & 128)==128)
		{
			//echo "Big Len ".($len & 127)." Bytes\r\n";
			for ($i = 0; $i < ($len & 127); $i++)
			  $le = $le * 256 + ord(fread($f,1));
			$len = $le;
		}
		//Findout data type (first 2 bits)
		$cl = ($type & (128+64) ) >> 6;
		switch($cl)
		{
		 case 0:
			$cla = 'Universal';
			break;
		 case 1:
			$cla = 'application';
			break;
		 case 2:
			$cla = 'context-specific';
			break;
		 case 3:
			$cla = 'private';
			break;
		}
		//Dump some info
		//echo sprintf("[$cla] Type: %x (%d), len: %d\r\n",$type,$type,$len);
		//Read data chunk
		if ($len == 0) {
			$data = null;
		} else {
			$data = fread($f, $len);
		}
		//Display data chunk based on type
		switch($type)
		{
		 case 2: // integer
			break;
		 case 3: // bit string
			break;
		 case 4: // octet string
			//printf("Data: $data\r\n");
			$plist = $data;
			break;
		 case 5: // null
			break;
		 case 6: // object identifier
			break;
		 case 16: // sequence and sequence of
			break;
		 case 17: // set and set of
			break;
		 case 19: //string
			//printf("Data: $data\r\n");
			break;
		 case 20: // t61string
			break;
		 case 22: // ia5string
			break;
		 case 23: // utctime
			break;
		 default:
			#printf("Data: $data\r\n");
			break;
		}
		//increment count
		$c++;
		//check if we reach the number of tag specified
		if (($c>$limit)&&($limit>0)) break;
	}
	 
	//close our input file
	fclose($f);
	return $plist;
}

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
if (empty($plistValue))
{
	die("unable to read plist from configuration profile challenge.");
}


$plist = new CFPropertyList();
$plist->parse( $plistValue, CFPropertyList::FORMAT_XML);


//echo '<pre>';
//var_dump( $plist->toArray() );
$plistData = $plist->toArray();

$lf->writeln('UDID='.$plistData['UDID']);
$lf->writeln('mail='.$_GET['mail']);
$lf->writeln('app='.$_GET['app']);
$lf->writeln('key='.$_GET['key']);
//echo '</pre>';

?>