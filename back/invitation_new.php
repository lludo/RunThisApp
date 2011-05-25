<?php

date_default_timezone_set('Europe/Berlin');

use Entities\Application, 
	Entities\Developer,
	Entities\Device,
	Entities\Invitation,
	Entities\Tester,
	Entities\Version;

require_once __DIR__ . '/../core/index.php';
require_once __DIR__ . '/../core/functions.php';

$entityManager = initDoctrine();


?><!doctype html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Run This App | New Invitation</title>
	<link href="../css/style-0001.css" media="screen" type="text/css" rel="stylesheet">
	
<style>
textarea {
width: 100%
}
</style>
</head>
<body>

	<div id="header">
		<h2><a href="../">Run This App</a></h2>
		<ul class="menu">
			<li class="invitations"><a href="invitation_list.php">Invitations</a></li>
			<li class="testers active">Testers</li>
        	<li class="applications"><a href="application_list.php">Applications</a></li>
		</ul>
		
	</div>
	
	<form action="invitation_send.php" method="POST">
	<p>
	Choose the application or (<a href="TODO">add a new application</a>):<br/>
	<select name="selected_version">
<?php
				// Retrieve all applications
				$applications = $entityManager->getRepository('Entities\Application')->findAll();

				foreach ($applications as $application) {
					$versions = $application->getVersions();
					foreach ($versions as $version) {
						echo '<option value="' . $version->getId() . '">' . $application->getName() . ' v' . $version->getVersion() . '</option>' . PHP_EOL;
					}
				}
?>	
	</select>
	<p>
	<?php
				// Retrieve all testers
				$testers = $entityManager->getRepository('Entities\Tester')->findAll();
				
				echo '<ul>';
				foreach ($testers AS $tester) {
					$devices = $tester->getDevices();
					foreach ($devices AS $device) {
						echo '<li>' . PHP_EOL;
						echo '  <input name="selected_devices[]" type="checkbox" value="' . $device->getId() . '"/>' . PHP_EOL;
						echo '  <span class="device-name">' . $device->getName() . '</span>' . PHP_EOL;
				    	echo '  <span class="device-udid">(' . substr($device->getUdid(), 0, 4) . '...)</span>' . PHP_EOL;
				    	echo '  <span class="tester-mail">' . $tester->getEmail() . '</span>' . PHP_EOL;
						echo '</li>' . PHP_EOL;
					}
				}
				echo '</ul>' . PHP_EOL;
				

	?>
	</p>
	<p>
	Mail message: <br/>
	<textarea name="body" rows="7"></textarea>
	<input type="submit" value="send" />
	</p>
	</form>
</body>
</html>