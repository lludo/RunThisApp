<?php

require_once __DIR__ . '/lib/cfpropertylist/CFPropertyList.php';
require_once __DIR__ . '/tools.php';

function general_payload() {
	
	$payload = array();
	$payload['PayloadVersion'] = 1; // do not modify
	$payload['PayloadUUID'] = uniqid(); // must be unique
	
	//will be shown to the user.
	$payload['PayloadOrganization'] = "RunThisApp";
	return $payload;
}

function profile_service_payload($challenge) {
    $payload = general_payload();

    $payload['PayloadType'] = "Profile Service"; // do not modify
    $payload['PayloadIdentifier'] = "com.runthisapp.mobileconfig.profile-service";

    // strings that show up in UI, customisable
    $payload['PayloadDisplayName'] = "RunThisApp Profile Service";
    $payload['PayloadDescription'] = "Install this profile to allow applications deployement from RunThisApp";
    $payload_content = array();
    $payload_content['URL'] = Tools::rel2abs('/profile.php?key=' . $_GET['key'], Tools::current_url());
    $payload_content['DeviceAttributes'] = array(
        'UDID', 
        'VERSION',
        'PRODUCT',              // ie. iPhone1,1 or iPod2,1
        'MAC_ADDRESS_EN0',      // WiFi MAC address
        'DEVICE_NAME',          // given device name "iPhone"
        // Items below are only available on iPhones
        'IMEI',
        'ICCID'
        );
    if (!empty($challenge)) {
        $payload_content['Challenge'] = $challenge;
    }

    $payload['PayloadContent'] = $payload_content;
    
	$plist = new CFPropertyList();

	$td = new CFTypeDetector();  
	$cfPayload = $td->toCFType( $payload );
	$plist->add( $cfPayload );
	return $plist->toXML(true);
}

header('Content-Type: application/x-apple-aspen-config');

$payload =  profile_service_payload('signed-auth-token');
echo $payload;

?>