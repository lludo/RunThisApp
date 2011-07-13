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
    
    use Entities\Application;
    use Entities\Version;
    use Entities\Tester;
    use Entities\Invitation;

    require_once __DIR__ . '/lib/cfpropertylist/CFPropertyList.php';
    require_once __DIR__ . '/core/index.php';
    require_once __DIR__ . '/tools.php';
    require_once __DIR__ . '/asn.php';

    function general_payload() {

            $payload = array();
            $payload['PayloadVersion'] = 1; // do not modify
            $payload['PayloadUUID'] = uniqid(); // must be unique

            //will be shown to the user.
            $payload['PayloadOrganization'] = "RunThisApp";
            return $payload;
    }

    function profile_service_payload($challenge, $key) {
        $payload = general_payload();

        $payload['PayloadType'] = "Profile Service"; // do not modify
        $payload['PayloadIdentifier'] = "com.runthisapp.mobileconfig.profile-service";

        // strings that show up in UI, customisable
        $payload['PayloadDisplayName'] = "RunThisApp Profile Service";
        $payload['PayloadDescription'] = "Install this profile to allow applications deployement from RunThisApp";
        $payload_content = array();
        $payload_content['URL'] = Tools::rel2abs('profile.php?key=' . $key, Tools::current_url());
        $payload_content['DeviceAttributes'] = array(
            'UDID', 
            'VERSION',
            'PRODUCT',              // ie. iPhone1,1 or iPod2,1
            'MAC_ADDRESS_EN0',      // WiFi MAC address
            'DEVICE_NAME',          // given device name "iPhone"
            // Items below are only available on iPhones
            'IMEI',
            'ICCID'
            );
        if (!empty($challenge)) {
            $payload_content['Challenge'] = $challenge;
        }

        $payload['PayloadContent'] = $payload_content;

            $plist = new CFPropertyList();

            $td = new CFTypeDetector();  
            $cfPayload = $td->toCFType( $payload );
            $plist->add( $cfPayload );
            return $plist->toXML(true);
    }

    function isAppSignedForUdid($udid, $application)
    {
		
        //TODO: Token folder should be on version, not application

        $plistValue = __DIR__ . '/app/' . $application->getToken() . '/app_bundle/Payload/' . $application->getName() . '.app/embedded.mobileprovision';
        $plistValue = retreivePlistFromAsn($plistValue);
        if (empty($plistValue)) {
            die("unable to read plist from configuration profile challenge.");
        }
        $plist = new CFPropertyList();
        $plist->parse($plistValue, CFPropertyList::FORMAT_AUTO);
        $plistData = $plist->toArray();
        $provisionedDevices = $plistData['ProvisionedDevices'];
        $found = false;
        foreach ($provisionedDevices as $device)
        {
            if (strtoupper($device) == strtoupper($udid)) {
                    return true;
            }
        }
        return false;
    }
    
    function generateDownloadPlistFile($application) {
        
        $payload_assets_content = array();
        $payload_assets_content['kind'] = 'software-package';
        $payload_assets_content['url'] = Tools::rel2abs('app/' . $application->getToken() . '/app_bundle.ipa', Tools::current_url());
        
        $payload_content = array();
        $payload_content['assets'] = array( $payload_assets_content );
        
        $payload_metadata_content = array();
        $payload_metadata_content['bundle-identifier'] = $application->getBundleId();
        $payload_metadata_content['kind'] = 'software';
        $payload_metadata_content['title'] = $application->getName();
                
        $payload_content['metadata'] = $payload_metadata_content;
        
        $payload = array();
        $payload['items'] = array( $payload_content );
        
        $plist = new CFPropertyList();

        $td = new CFTypeDetector();  
        $cfPayload = $td->toCFType( $payload );
        $plist->add( $cfPayload );
        $data = $plist->toXML(true);
        
        $my_file = __DIR__ . '/app/' . $application->getToken() . '.plist';
        $handle = fopen($my_file, 'w') or die('Cannot open file:  '.$my_file);
        fwrite($handle, $data);
    }

    //Step 1 check needed params:
    if (!isset($_GET['token']))
    {
        die('parameter token are needed (udid optional)');
    }

    //step 2 check that user is allowed to dl this app and this version:
    //TODO using $_GET["udid"]
    $entityManager = initDoctrine();
    date_default_timezone_set('Europe/Paris');

    $invitation = $entityManager->getRepository('Entities\Invitation')->findOneBy(array('token' => $_GET['token']));
    if ( $invitation == NULL ) {
        die('This invitation token is not valid!');
    }

    if (!isset($_GET['udid'])) {
        $action = 'ENROLL';
        $isNotRegistered = ($invitation->getStatus() == Invitation::STATUS_SENT);
    } else {
        $action = 'DOWNLOAD';
    }
	$application = $invitation->getVersion()->getApplication();

    if ($action == 'ENROLL' && $isNotRegistered) {

        $mail = $invitation->getTester()->getEmail();
        header('Content-Type: application/x-apple-aspen-config');

        $payload =  profile_service_payload('signed-auth-token', $_GET['token']);
        echo $payload;

        die ();
    }

    //step3 check that this app is already signed for this udid
    //if not sign it.
    $isAppSigned = isAppSignedForUdid($_GET['udid'], $application);
    
    $isAppSigned = false;
    
    //step4 provid link to dld app
    generateDownloadPlistFile($application);
    
    $appLink = Tools::rel2abs('app/'. $application->getToken() .'.plist', Tools::current_url());
    $profileLink = Tools::rel2abs('app/'. $application->getToken() .'.mobileprovision', Tools::current_url());
    
?>
<html>
<head>

    <script src="js/jquery-1.6.1.min.js"></script>

    <script>

    function showTheLink(bool) {
            if (bool) {
                    $("#link").css("display", "inline");
                    $("#wait").css("display", "none");
            } else {
                    $("#link").css("display", "none");
                    $("#wait").css("display", "inline");
            }
    }

    </script>

    <style>
    #link {
            display: none;
    }
    </style>
</head>
<body>
    <span id="link">
        Here is your link: <a href="itms-services://?action=download-manifest&url=<?php echo $appLink; ?>">Application id <?php echo $application->getBundleId(); ?></a>
        (Profile link: <a href="<?php echo $profileLink; ?>">Application profile</a>)
    </span>
    <?php
    
    if ($action == 'ENROLL') {
        if (!$isNotRegistered) {
            echo '<div>Your device is already registered!</div>';
        }
    }
    else if ($action == 'DOWNLOAD') {
        if ($isAppSigned) {
    ?>
            <script>
                showTheLink(true);
            </script>
        <?php
        } else {
        ?>
            <span id="wait">
                Please Wait...
            </span>
            <script>
	            $.ajax({
                    type: "GET",
                    url: "sign.php",
                    data: "token=<?php echo $_GET['token']; ?>&udid=<?php echo $_GET['udid']; ?>",
                    success: function(msg){	
                    	showTheLink(true);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                    	var msg = $("span#wait").empty().append("Unable to sign!");
                    	if (jqXHR && jqXHR.responseText) {
                    		//append error msg
                        	msg.append(' ' . jqXHR.responseText);
                    	}
                    }
	             });
            </script>
    <?php
        }
    }
    ?>
</body>
</html>
