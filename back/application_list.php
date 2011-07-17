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
   
   use Entities\Application, 
        Entities\Developer,
        Entities\Device,
        Entities\Invitation,
        Entities\Tester,
        Entities\Version;
    
    require_once __DIR__ . '/../constants.php';
    require_once __DIR__ . '/../core/index.php';
    require_once __DIR__ . '/../tools.php';
    
    $entityManager = initDoctrine();
    
    // Retrieve all testers
    $applications = $entityManager->getRepository('Entities\Application')->findAll();
    
?>

<!doctype html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Run This App | Applications</title>
	<link href="../css/style-0001.css" media="screen" type="text/css" rel="stylesheet">
</head>
<body>

	<?php include __DIR__ . '/../header.php';?>

	
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
				
				echo '<ul>';
				foreach ($applications AS $application) {
				    echo '<li>Application: <br/>'
                                        .'->bundle name: ' . $application->getBundleName() . '<br />'
                                        .'->bundle id: ' . $application->getBundleId() . '<br />'
                                        .'->icon: <img src="'.'../'.UPLOAD_PATH. $application->getBundleId().'.png"><br />';
				    	
                                    echo '<ul>';
                                    foreach ($application->getVersions() AS $version) {
                                	echo '<li>Version:<br/>'
                                        .'->version: '.$version->getVersion().'<br />'
				    	.'->date upload: '.$version->getDateUpload()->format(DEFAULT_DATETIME_FORMAT).'<br />'
				    	.'->app link: <a href="' . Tools::rel2abs('../app/' . $version->getToken() . '/app_bundle.ipa', Tools::current_url()) . '">' . $version->getName() . '.ipa</a><br />'
				    	.'->install on device: <a href="itms-services://?action=download-manifest&url=' . Tools::rel2abs('../app/' . $version->getToken() . '.plist', Tools::current_url()) . '">Install on device</a></li>' . PHP_EOL;
                                        
                                    }
                                    echo '</ul>' . PHP_EOL;
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