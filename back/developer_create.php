<?php

session_start();

use Entities\Developer;

require_once __DIR__ . '/../core/index.php';
require_once __DIR__ . '/../core/functions.php';
require_once __DIR__ . '/../core/Membership.php';
require_once __DIR__ . '/../tools.php';

if (!isset($_POST['name'], $_POST['email'], $_POST['pwd']) ) {
    die('parameters needed.'); 
}

$entityManager = initDoctrine();

$developer = $entityManager->getRepository('Entities\Developer')->findOneBy(array('email' => $_POST['email']));
if ($developer != NULL) {
    die('A developer with the email adress '.$_POST['email'].' already exists.'); 
}

$developer = new Developer();
$developer->setName($_POST['name']);
$developer->setEmail($_POST['email']);
$pwd = sha1($_POST['pwd']);
$developer->setPassword($pwd);

$entityManager->persist($developer);
$entityManager->flush();

//TODO add validation step before creating the developer.

?><!doctype html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Run This App | Register</title>
	<link href="../css/style-0001.css" media="screen" type="text/css" rel="stylesheet">
</head>
<body>
	<?php include __DIR__ . '/../header.php';?>
    
    <p>
        An email has been sent to <?php echo $_POST['email'];?> in order to validate your mail. 
        Please check your email and click on the link found in it.
    </p>
	
</body>
</html>
