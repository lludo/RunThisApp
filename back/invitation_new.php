<?php 

/*
 *    RunThisApp allows sharing test builds of iOS apps with testers.
 *    Copyright (C) 2011 Ludovic Landry & Pascal Cans
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

session_start();

use Entities\Application, 
	Entities\Developer,
	Entities\Device,
	Entities\Invitation,
	Entities\Tester,
	Entities\Version;

require_once __DIR__ . '/../core/index.php';
require_once __DIR__ . '/../core/functions.php';
require_once __DIR__ . '/../core/Membership.php';

if (!Membership::isLoggedIn()) {
    header('Location: ../index.php');
    die();
}

$entityManager = initDoctrine();

?><!doctype html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Run This App | New Invitation</title>
	<link href="../css/style-0001.css" media="screen" type="text/css" rel="stylesheet">
        <script src="../js/jquery-1.6.1.min.js"></script>
	
<style>
textarea {
    width: 100%
}
</style>
</head>
<body>

	<?php include __DIR__ . '/../header.php';?>

	
	<form action="invitation_send.php" method="POST">
	<p>
	Choose the application or (<a href="application_new.php">add a new application</a>):<br/>
	<select name="selected_version">
            <?php
                // Retrieve all applications
                $applications = $entityManager->getRepository('Entities\Application')->findAll();

                foreach ($applications as $application) {
                        $versions = $application->getVersions();
                        foreach ($versions as $version) {
                                echo '<option value="' . $version->getId() . '">' . $version->getName() . ' v' . $version->getVersion() . '</option>' . PHP_EOL;
                        }
                }
            ?>	
	</select>
	<div>
            <ul>
                <?php
                    // Retrieve all testers
                    $testers = $entityManager->getRepository('Entities\Tester')->findAll();

                    foreach ($testers AS $tester) {
                            $devices = $tester->getDevices();
                            foreach ($devices AS $device) {
                                echo '<li>' . PHP_EOL;
                                echo '  <input name="selected_devices[]" type="checkbox" value="' . $device->getId() . '"/>' . PHP_EOL;
                                echo '  <span class="device-name">' . $device->getName() . '</span>' . PHP_EOL;
                                echo '  <span class="device-udid">[' . substr($device->getUdid(), 0, 6) . '...]</span>' . PHP_EOL;
                                echo '  <span class="tester-mail">' . $tester->getEmail() . '</span>' . PHP_EOL;
                                echo '</li>' . PHP_EOL;
                            }
                    }
                ?>
                <li>
                    <input name="selected_device_new" type="checkbox" value="new" id="selected_device_new"/>
                    <span class="device-name">New device</span>
                    <span class="device-udid">[??????...]</span>
                    <span class="tester-mail">
                        <select name="selected_tester" id="selected_tester" disabled >
                            <?php
				foreach ($testers AS $tester) {
                                    echo '<option value="' . $tester->getEmail() . '">' . $tester->getEmail() . '</option>' . PHP_EOL;
				}
                             ?>
                            <option value="new_tester">Add a new tester</option>
                        </select>
                        <input type="email" name="new_tester_email" placeholder="Tester email" id="new_tester_email" hidden />
                    </span>
                </li>
            </ul>
	</div>
	<p>
	Mail message: <br/>
	<textarea name="body" rows="7"></textarea>
	<input type="submit" value="send" />
	</p>
    </form>
    <script>
        $('#selected_device_new').click(onNewDeviceChecked);
        $('#selected_tester').change(onTesterSelected);
        
        function onNewDeviceChecked() {

            if ( $('#selected_device_new').is(':checked') ) {
                $('#selected_tester').removeAttr('disabled');
            } else {
                $('#selected_tester').attr('disabled', true);
            }
            
            onTesterSelected();
        }
        
        function onTesterSelected() {
           
           if ( ($('#selected_tester').val() === 'new_tester')
           && ($('#selected_device_new').is(':checked')) ) {
                $('#new_tester_email').removeAttr('hidden');
           } else {
                $('#new_tester_email').attr('hidden', true);
           }
        }
    </script>
</body>
</html>
