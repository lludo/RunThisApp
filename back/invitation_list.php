<!doctype html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Run This App | Invitations</title>
	<link href="../css/style-0001.css" media="screen" type="text/css" rel="stylesheet">
</head>
<body>
	
	<div id="header">
		<h2><a href="../">Run This App</a></h2>
		<ul class="devcenterMenu">
			<li class="invitations active">Invitations</li>
			<li class="testers"><a href="tester_list.php">Testers</a></li>
	    	<li class="applications"><a href="application_list.php">Applications</a></li>
		</ul>
		
		<ul class="login">
			<li>Hi, Guest</li>
			<li><a href="register.php">Register</a></li>	
			<li><a href="login.php">Log In</a></li>
		</ul>
	</div>
	
	<div id="content" class="grid2colb box grid2colb-box">
		<div class="cap boxtop"></div>
		<div class="column first grid2col">
			<div class="boxheader">			
				<h2>Manage Invitations</h2>
			</div>
			<div>
				
				<h3 class="underline">&nbsp;</h3>
				<a href="invitation_new.php">Send a new invitation</a>
				
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
				$invitations = $entityManager->getRepository('Entities\Invitation')->findAll();
				
				echo '<ul>';
				foreach ($invitations AS $invitation) {
				    echo '<li>Invitation: ' . $invitation->getTester()->getEmail() . '</li>' . PHP_EOL;
				}
				echo '</ul>' . PHP_EOL;
				
				?>
				
			</div>
		</div>
		
		<div class="column last">
			<div class="boxheader">
				<h2>Deployment steps</h2>
			</div>
			<div class="promo quickstart">
				<h6>Send Invitations</h6>
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
			</div>
			<hr>
			<div class="promo quickstart">
				<h6>Tester get registered</h6>
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
			</div>
			<hr>
			<div class="promo quickstart">
				<h6>They install your app Over-The-Air</h6>				
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
			</div>
		</div>	
		<div class="cap boxbottom"></div>
	</div>

</body>
</html>