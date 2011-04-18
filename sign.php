<?php

require_once __DIR__ . '/lib/cfpropertylist/CFPropertyList.php';
require_once __DIR__ . '/asn.php';
require_once __DIR__ . '/credentials.php';

//Step 1 check needed params:
if (!isset($_GET['udid'], $_GET['app'], $_GET['ver']))
{
	die('parameters udid, app and ver are needed');
}

function connect($user, $pwd){
	/*
	HTTP/1.1
	Host: daw2.apple.com
	User-Agent: Xcode
	Accept-Encoding: gzip, deflate
	Content-Type: text/x-xml-plist
	Accept: text/x-xml-plist
	AcceptGLanguage: en-us
	Cookie: ""
	Connection: keep-alive
	*/
		$url= 'https://daw2.apple.com/cgi-bin/WebObjects/DSAuthWeb.woa/wa/clientDAW?format=plist&appIdKey=D136F3CA19FC87ADBC8514E10325B1000184218304C8DB66713C0CB40291F620&appleId='.urlencode($user).'&password='.urlencode($pwd).'&userLocale=en_US&protocolVersion=A1234';
	

	// create a new cURL resource
	$ch = curl_init();

	// set URL and other appropriate options
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'User-Agent: Xcode',
		
		'Accept: text/x-xml-plist',
		'Accept-Language: en-us',
		'Connection: keep-alive')); 
		//'Content-Type: text/x-xml-plist',
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	//curl_setopt($ch, CURLOPT_POST, true);
	//curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
	curl_setopt($ch, CURLOPT_ENCODING , 'gzip, deflate');
	//TODO do not use a file
	curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt'); 
	curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt'); 
	
    curl_setopt($ch, CURLOPT_HEADER, true);
    //curl_setopt($ch, CURLOPT_POSTFIELDS, $contents);
	
	//TODO secure the connection
	//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	//curl_setopt($ch, CURLOPT_CAINFO, __DIR__ . '/certs/VeriSignCA.pem');
	
	// grab URL and pass it to the browser
	$head = curl_exec($ch); 
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
	echo "url= $url</br>";
	echo "httpCode= $httpCode</br>";
	echo "head= $head</br>";
	
	// close cURL resource, and free up system resources
	curl_close($ch);
	
	
	
	}
	
	function viewDeveloper(){

	
	
	$url= 'https://connect1.apple.com/services/GL9N1P/viewDeveloper.action?clientId=XABBG36SBA?clientId=XABBG36SBA';
	
	$contents = '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN"
"http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
 <key>clientId</key>
 <string>XABBG36SBA</string>
 <key>protocolVersion</key>
 <string>GL9N1P</string>
 <key>requestId</key>
 <string>FFE3A771-193F-4311-88BD-566797548E11</string>
 <key>userLocale</key>
 <array>
  <string>en_US</string>
 </array>
</dict>
</plist>';

	// create a new cURL resource
	$ch = curl_init();

	// set URL and other appropriate options
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'User-Agent: Xcode',
		'Content-Type: text/x-xml-plist',
		'Accept: text/x-xml-plist',
		'Accept-Language: en-us',
		'Connection: keep-alive')); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_ENCODING , 'gzip, deflate');
	//TODO do not use a file
	curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt'); 
	curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt'); 
	
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $contents);
	
	//TODO secure the connection
	//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	//curl_setopt($ch, CURLOPT_CAINFO, __DIR__ . '/certs/VeriSignCA.pem');
	
	// grab URL and pass it to the browser
	$head = curl_exec($ch); 
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
	echo "url= $url</br>";
	echo "httpCode= $httpCode</br>";
	echo "head= $head</br>";
	
	// close cURL resource, and free up system resources
	curl_close($ch);

}


connect($CRED_USR, $CRED_PWD);
viewDeveloper();

?>