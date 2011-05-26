<?php

require_once __DIR__ . '/lib/cfpropertylist/CFPropertyList.php';
require_once __DIR__ . '/asn.php';

//Step 1 check needed params:
if (!isset($_GET['udid'], $_GET['app'], $_GET['ver']))
{
	die('parameters udid, app and ver are needed');
}



//step 2 check that user is allowed to dl this app and this version:
//TODO using $_GET["udid"]


//step3 check that this app is already signed for this udid
//if not sign it.
function isAppSignedForUdid($udid, $app, $ver)
{
	$plistValue = __DIR__ . '/app/AppSalesMobile/Payload/AppSalesMobile.app/embedded.mobileprovision';
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

$isAppSigned = isAppSignedForUdid($_GET['udid'], $_GET['app'], $_GET['ver']);

//step4 provid link to dld app

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
here is your link: <a href="toto.ipa"><?php echo $_GET['app'] ?></a>
</span>
<?php
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
	type: "POST",
	url: "sign.php",
	data: "app=<?php echo $_GET['app'] ?>&ver=<?php echo $_GET['ver'] ?>&udid=<?php echo $_GET['udid'] ?>",
	success: function(msg){	
		showTheLink(true);
	}
 });
</script>
<?php
}
?>
</body>
</html>
