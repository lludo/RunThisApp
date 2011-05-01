<?php

require_once __DIR__ . '/lib/cfpropertylist/CFPropertyList.php';
require_once __DIR__ . '/asn.php';
require_once __DIR__ . '/credentials.php';
require_once __DIR__ . '/core/apple-services.php';

//Step 1 check needed params:
if ( !isset($_GET['udid'], $_GET['app'], $_GET['ver']) ) {
    die ('parameters udid, app and ver are needed');
}

//TODO: Check if the UDID is not already in the profile in the app
// in that case we don't have to register to apple

$result = AppleServices::connect($CRED_USR, $CRED_PWD);
if ( $result['resultCode'] != 0 ) {
    die ($result['userString']);
}
echo '<p>Connected!</p>' . PHP_EOL;

$result = AppleServices::viewDeveloper();
if ( $result['resultCode'] != 0 ) {
    die ($result['userString']);
}
echo '<p>Developer info retrieved.</p>' . PHP_EOL;

$result = AppleServices::listTeams();
if ( $result['resultCode'] != 0 ) {
    die ($result['userString']);
}
echo '<p>Developer teams listed.</p>' . PHP_EOL;

// TODO: For now we only take care about the one team case
$team_id = $result['teams'][0]['teamId'];

$result = AppleServices::listDevices($team_id);
if ( $result['resultCode'] != 0 ) {
    die ($result['userString']);
}
echo '<p>Devices listed.</p>' . PHP_EOL;

$device_found = false;
foreach ($result['devices'] as $device) {
    if ($_GET['udid'] == $device['deviceNumber']) {
        $device_found = true;
        break;
    }
}

if ( $device_found ) {
    echo '<p>The device is already registered.</p>' . PHP_EOL;
    echo '<p>UDID: ' . $device['deviceNumber'] . '</p>' . PHP_EOL;
    echo '<p>deviceId: ' . $device['deviceId'] . '</p>' . PHP_EOL;
    echo '<p>name: ' . $device['name'] . '</p>' . PHP_EOL;
    //TODO: $result = AppleServices::updateDevice($deviceId);
} else {
    
    echo '<p>This device is not yet registered.</p>' . PHP_EOL;
    $result = AppleServices::addDevice($_GET['udid'], 'iPhone de karim', $team_id);
    
    if ($result['resultCode'] != 0) {
        die($result['userString']);
    }
    echo '<p>Device added.</p>' . PHP_EOL;
    
    foreach ($result['devices'] as $device) {
        if ($_GET['udid'] == $device['deviceNumber']) {
            $device_id = $device['deviceId']; // this is an id usefull for WS not the UDID
            break;
        }
    }
    
    echo '<p>Device id on server:' . $device_id . '</p>' . PHP_EOL;
}

//TODO: add the $device_id for the provisioning profile application from $_GET['app']
//TODO: then regenerate this profile and download it

?>
