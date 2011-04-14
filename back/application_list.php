<!doctype html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Run This App | Applications</title>
	<link href="../css/style-0001.css" media="screen" type="text/css" rel="stylesheet">
</head>
<body>

	<div id="header">
		<h2><a href="../">Run This App</a></h2>
		<ul class="menu">
			<li class="invitations"><a href="invitation_list.php">Invitations</a></li>
			<li class="testers"><a href="tester_list.php">Testers</a></li>
        	<li class="applications active">Applications</li>
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
				<h2>Manage Applications</h2>
			</div>
			<div>
			
				<a href="application_new.php">Add a new application</a>
				<h3 class="underline">&nbsp;</h3>
				
				<?php
				
				use Entities\Application, 
				    Entities\Developer,
				    Entities\Device,
				    Entities\Invitation,
				    Entities\Tester,
				    Entities\Version;
				
				require_once __DIR__ . '/../core/index.php';
				
				$entityManager = initDoctrine();
				
				// Retrieve all testers
				date_default_timezone_set('Europe/Berlin');
				$applications = $entityManager->getRepository('Entities\Application')->findAll();
				
				echo '<ul>';
				foreach ($applications AS $application) {
				    echo '<li>Application: <br/>->name: ' . $application->getName() . '<br />' . 
				    	'->bundle: ' . $application->getBundleId() . '<br />' . 
				    	'->app link: <a href="../app/' . $application->getName() . '.ipa">../app/' . $application->getName() . '.ipa</a><br />' . 
				    	'->install on device: <a href="itms-services://?action=download-manifest&url=http://www.runthisapp.com/app/' . $application->getName() . '.plist">Install on device</a></li>' . PHP_EOL;
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