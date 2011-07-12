<?php

use Entities\Application;

require_once __DIR__ . '/tools.php';
require_once __DIR__ . '/lib/cfpropertylist/CFPropertyList.php';
require_once __DIR__ . '/asn.php';
require_once __DIR__ . '/credentials.php';
require_once __DIR__ . '/core/apple-services.php';
require_once __DIR__ . '/core/index.php';

//Step 1 check needed params:
if ( !isset($_GET['udid'], $_GET['token']) ) {
    Tools::dieError('parameters udid and token are needed');
}

//TODO: Check if the UDID is not already in the profile in the app
// in that case we don't have to register to apple

//TODO: ensure udid had been invited to test this app.

//load application from database.
$entityManager = initDoctrine();
//TODO do not use specific tz
date_default_timezone_set('Europe/Paris');

$invitation = $entityManager->getRepository('Entities\Invitation')->findOneBy(array('token' => $_GET['token']));
if ( $invitation == NULL ) {
    Tools::dieError('This invitation token is not valid!');
}
$application = $invitation->getVersion()->getApplication();


$result = AppleServices::connect($CRED_USR, $CRED_PWD);
if ( $result['resultCode'] != 0 ) {
    Tools::dieError($result['userString']);
}
echo '<p>Connected!</p>' . PHP_EOL;

$result = AppleServices::viewDeveloper();
if ( $result['resultCode'] != 0 ) {
    Tools::dieError($result['userString']);
}
echo '<p>Developer info retrieved.</p>' . PHP_EOL;

$result = AppleServices::listTeams();
if ( $result['resultCode'] != 0 ) {
    Tools::dieError($result['userString']);
}
echo '<p>Developer teams listed.</p>' . PHP_EOL;

// TODO: For now we only take care about the one team case
$team_id = $result['teams'][0]['teamId'];

$result = AppleServices::listDevices($team_id);
if ( $result['resultCode'] != 0 ) {
    Tools::dieError($result['userString']);
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
    //TODO load $device and gat name to add it to Apple.
    $result = AppleServices::addDevice($_GET['udid'], 'iPhone de TODO', $team_id);
    
    if ($result['resultCode'] != 0) {
        Tools::dieError($result['userString']);
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

$result = AppleServices::listProvisioningProfiles($team_id);
if ( $result['resultCode'] != 0 ) {
    Tools::dieError($result['userString']);
}

$profile_id_found = NULL;
foreach ($result['provisioningProfiles'] as $profile) {
    if ($application->getBundleId() == $profile['appId']['identifier']) {
        $profile_id_found = $profile['provisioningProfileId'];
        break;
    }
}

if ( $profile_id_found != NULL ) {
    echo '<p>Profile found for app: ' . $application->getBundleId() . ' (' . $profile_id_found . ')</p>' . PHP_EOL;
} else {
    Tools::dieError('Profile not found for the app requested!');
}

$result = AppleServices::editProvisioningProfile($profile_id_found, $_GET['udid']);
if ( !$result ) {
    Tools::dieError('Cant add this device to the provisioning profile!');
}

echo '<p>Device added to profile.</p>' . PHP_EOL;

// Get the new provisioning profile id (it changes after each modification)
$new_profile_id = $profile_id_found;
$iterCount = 0;
while ( strcmp($new_profile_id, $profile_id_found) == 0 ) {
    
    $result = AppleServices::listProvisioningProfiles($team_id);
    if ( $result['resultCode'] != 0 ) {
        Tools::dieError($result['userString']);
    }
    
    foreach ($result['provisioningProfiles'] as $profile) {
        if ($application->getBundleId() == $profile['appId']['identifier']) {
            $new_profile_id = $profile['provisioningProfileId'];
            break;
        }
    }
    //do something to avoid stackoverflow in case of long generation
    if (++$iterCount > 50) {
    	Tools::dieError('Profile not yet updated by Apple after '.$iterCount.' iterations. abandon.');
    }
}

echo '<p>New profile id: ' . $new_profile_id . '</p>' . PHP_EOL;

$result = AppleServices::downloadProvisioningProfile($new_profile_id, $team_id);

if ( $result['resultCode'] != 0 ) {
    if ( $result['userString'] != NULL ) {
        Tools::dieError($result['userString']);
    } else {
        echo '<p>Error downloading the new profile.</p>' . PHP_EOL;
        var_dump($result);
    }
}

$new_profile = $result['provisioningProfile']['encodedProfile'];

//TODO: save this with the app binary (tmp: save in app folder with bundle id)
$newfile = __DIR__ . '/app/' . $application->getToken() . '.mobileprovision';
$file = fopen ($newfile, "w");
fwrite($file, $new_profile);
fclose ($file); 

echo '<p>New profile saved.</p>' . PHP_EOL;

?>
