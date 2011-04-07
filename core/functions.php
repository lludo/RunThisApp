<?php

function getReadableDeviceName($deviceModel) {
	
	$devices = array(
	'iPhone1,1' => 'iPhone',
	'iPhone1,2' => 'iPhone 3G',
	'iPhone2,1' => 'iPhone 3GS',
	'iPhone3,1' => 'iPhone 4 (GSM)',
	'iPhone3,2' => 'iPhone 4 (CDMA)',
	'iPod1,1' => 'iPod Touch (1st generation)',
	'iPod2,1' => 'iPod Touch (2nd generation)',
	'iPod2,2' => 'iPod Touch (2nd end generation)',
	'iPod3,1' => 'iPod Touch (3rd generation)',
	'iPod4,1' => 'iPod Touch (4th generation)',
	'iPad1,1' => 'iPad (WiFi)',
	'iPad1,2' => 'iPad (WiFi+3G)',
	'iPad2,1' => 'iPad 2 (WiFi)',
	'iPad2,2' => 'iPad 2 (WiFi+3G GSM)',
	'iPad2,3' => 'iPad 2 (WiFi+3G CDMA)',
	'Apple2,1' => 'Apple TV (2nd generation)',
	'i386' => 'iOS Simulator');
	
	//TODO: add error management if device is unknown
	
	return $devices[$deviceModel];
}

function getReadableOsVersion($osBuild) {

	$os = array(
	'1A543a' => '1.0',
	'1C25' => '1.0.1',
	'1C28' => '1.0.2',
	'3A100a' => '1.1',
	'3A101a' => '1.1',
	'3A109a' => '1.1.1',
	'3A110a' => '1.1.1',
	'3B48b' => '1.1.2',
	'4A93' => '1.1.3',
	'4A102' => '1.1.4',
	'4B1' => '1.1.5',
	'5A347' => '2.0',
	'5B108' => '2.0.1',
	'5C1' => '2.0.2',
	'5F136' => '2.1',
	'5F137' => '2.1',
	'5F138' => '2.1',
	'9M2517' => '2.1',
	'5G77' => '2.2',
	'5G77a' => '2.2',
	'5H11' => '2.2.1',
	'5H11a' => '2.2.1',
	'9M2621a' => '2.2.1',
	'7A341' => '3.0',
	'7A400' => '3.0.1',
	'7C144' => '3.1',
	'7C145' => '3.1',
	'7C146' => '3.1',
	'7D11' => '3.1.2',
	'7E18' => '3.1.3',
	'7B367' => '3.2',
	'7B405' => '3.2.1',
	'7B500' => '3.2.2',
	'8A293' => '4.0',
	'8A306' => '4.0.1',
	'8A400' => '4.0.2',
	'8B117' => '4.1',
	'8C134' => '4.2',
	'8C134b' => '4.2',
	'8C148' => '4.2.1',
	'8C148a' => '4.2.1',
	'8E128' => '4.2.5',
	'8E200' => '4.2.6',
	'8F190' => '4.3',
	'8G4' => '4.3.1');
	
	//TODO: add error management if build is unknown
	
	return $os[$osBuild];
}

?>
