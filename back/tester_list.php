<!doctype html>
<html>
<head>
	<title>Run This App | Testers</title>
</head>
<body>
	<a href="tester_new.php">Add a new tester</a>
<?php

use Entities\Application, 
    Entities\Developer,
    Entities\Device,
    Entities\Invitation,
    Entities\Tester,
    Entities\Version;

require_once __DIR__ . '/../core/index.php';
require_once __DIR__ . '/../core/functions.php';

$entityManager = initDoctrine();

// Retrieve all testers
date_default_timezone_set('Europe/Berlin');
$testers = $entityManager->getRepository('Entities\Tester')->findAll();

echo '<ul>';
foreach ($testers AS $tester) {
    echo '<li>Tester: ' . $tester->getName() . '</br > Devices: ' . PHP_EOL;
    	
    	$devices = $tester->getDevices();
    	
    	// If the tester have no devices
    	if ($devices->isEmpty()) {
    		echo 'No devices registered yet.' . PHP_EOL;
    		
    	// Display all devices from the tester
    	} else {
    		echo '<ul>' . PHP_EOL;
    		foreach ($tester->getDevices() AS $device) {
	        	echo '<li>Device: ' . getReadableDeviceName($device->getModel()) . ', UDID: ' . $device->getUdid() . '</li>' . PHP_EOL;
	        }
	        echo '</ul>' . PHP_EOL;
        }
    echo '</li>' . PHP_EOL;
}
echo '</ul>' . PHP_EOL;

?>
</body>
</html>