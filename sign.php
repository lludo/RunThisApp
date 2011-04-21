<?php

require_once __DIR__ . '/lib/cfpropertylist/CFPropertyList.php';
require_once __DIR__ . '/asn.php';
require_once __DIR__ . '/credentials.php';
require_once __DIR__ . '/core/apple-services.php';

//Step 1 check needed params:
if ( !isset($_GET['udid'], $_GET['app'], $_GET['ver']) ) {
    die ('parameters udid, app and ver are needed');
}


$result = AppleServices::connect($CRED_USR, $CRED_PWD);
//echo var_dump($result);
if ( $result['resultCode'] != 0 ) {
    //die ($result['userString']);
}


$result = AppleServices::viewDeveloper();
//echo var_dump($result);
if ( $result['resultCode'] != 0 ) {
    //die ($result['userString']);
}
     

$result = AppleServices::listTeams();
//echo var_dump($result);
if ( $result['resultCode'] != 0 ) {
    die ($result['userString']);
}

// TODO: For now we only take care about the one team case
$team_id = $result['teams'][0]['teamId'];


$result = AppleServices::listDevices($team_id);
//echo var_dump($result);
if ( $result['resultCode'] != 0 ) {
    die ($result['userString']);
}

$device_found = false;
foreach ($result['devices'] as $device) {
    if ($_GET['udid'] == $device['deviceNumber']) {
        $device_found = true;
        break;
    }
}

if ( $device_found ) {
    echo 'The device is already registered.<br/>' . PHP_EOL;
    echo 'UDID: ' . $device['deviceNumber'] . '<br/>' . PHP_EOL;
    echo 'deviceId: ' . $device['deviceId'] . '<br/>' . PHP_EOL;
    echo 'name: ' . $device['name'] . '<br/>' . PHP_EOL;
    //$result = AppleServices::updateDevice($deviceId);
} else {
    echo 'This device is not registered';
    //$result = AppleServices::addDevice();
}

?>
