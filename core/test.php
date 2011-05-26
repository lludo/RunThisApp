<?php



require_once __DIR__ . '/apple-services.php';



// ID that we want to add to profile
$user = 'CIT-DSTD';
$pwd = 'clamelo12';
$provisioning_profile_id = 'E64WAB27PF';
$udid_to_add = '50c229e4b5a0c242caaf4f1b95ca63384c08a290';

$result = AppleServices::editProvisioningProfile($user, $pwd, $provisioning_profile_id, $udid_to_add);

?>
