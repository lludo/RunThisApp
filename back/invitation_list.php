<!doctype html>
<html>
<head>
	<title>Run This App | Invitations</title>
</head>
<body>
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
</body>
</html>