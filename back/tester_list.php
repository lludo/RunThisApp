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

?><!doctype html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Run This App | Testers</title>
	<link href="../css/style-0001.css" media="screen" type="text/css" rel="stylesheet">
</head>
<body>

	<div id="header">
		<h2><a href="../">Run This App</a></h2>
		<ul class="menu">
			<li class="invitations"><a href="invitation_list.php">Invitations</a></li>
			<li class="testers active">Testers</li>
        	<li class="applications"><a href="application_list.php">Applications</a></li>
		</ul>
		
		<ul class="login">
			<li>Hi, Guest</li>
			<li><a href="register.php">Register</a></li>	
			<li><a href="login.php">Log In</a></li>
		</ul>
	</div>
	
	<div id="content" class="box">
		<div class="boxtop"></div>
		<div class="column first">
			<div class="boxheader">			
				<h2>Manage Testers</h2>
			</div>
			<div>
			
				<a href="tester_new.php">Add a new tester</a>
				<h3 class="underline">&nbsp;</h3>
				
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
				    echo '<li>Tester: ' . $tester->getName() . '</br >' . PHP_EOL;
				    	
				    	$devices = $tester->getDevices();
				    	
				    	// If the tester have no devices
				    	if ($devices->isEmpty()) {
				    		echo 'No devices registered yet.' . PHP_EOL;
				    		
				    	// Display all devices from the tester
				    	} else {
				    		echo '<ul>' . PHP_EOL;
				    		foreach ($tester->getDevices() AS $device) {
					        	echo '<li>->Device: ' . $device->getName() . ', Model: ' . getReadableDeviceName($device->getModel()) . ', UDID: ' . $device->getUdid() . '</li>' . PHP_EOL;
					        }
					        echo '</ul>' . PHP_EOL;
				        }
				    echo '<h3 class="underline">&nbsp;</h3>' . PHP_EOL;
				    echo '</li>' . PHP_EOL;
				}
				echo '</ul>' . PHP_EOL;
				
				?>
	
			</div>
		</div>
		
		<div class="column last">
			<div class="boxheader">
				<h2>Deployment steps</h2>
			</div>
			<div class="function">
				<h6>Send Invitations</h6>
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
			</div>
			<hr>
			<div class="function">
				<h6>Tester get registered</h6>
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
			</div>
			<hr>
			<div class="function">
				<h6>They install your app Over-The-Air</h6>				
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
			</div>
		</div>	
		<div class="boxbottom"></div>
	</div>

</body>
</html>