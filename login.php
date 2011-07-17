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
use Entities\Developer;

session_start();
require_once __DIR__ . '/core/Membership.php';

// If the user clicks the "Logout" link on the index page.
if(isset($_GET['action']) && $_GET['action'] == 'logout') {
        Membership::logout();
        header('Location: index.php');
}

if(isset($_GET['expired'])) {
    $message = 'Your session has expired, please log in again.';
}


if (isset($_POST['email'],$_POST['pwd'])) {
    
    require_once __DIR__ . '/core/index.php';
    require_once __DIR__ . '/core/functions.php';
    require_once __DIR__ . '/tools.php';

    $entityManager = initDoctrine();
    
    $validated = Membership::validate($entityManager, $_POST['email'], $_POST['pwd']);
    if ($validated) {
        header('Location: index.php');
    } else {
        $message = 'invalid login and/or password';
    }
    
}


?><!doctype html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Run This App | Login</title>
	<link href="css/style-0001.css" media="screen" type="text/css" rel="stylesheet">
</head>
<body>

    <?php include(__DIR__ . '/header.php'); ?>

    <?php 
    if (isset ($message)){
        echo '<p id="error">'.$message.'</p>';
    } 
    ?>
    <form method="POST" action="">
        <p>
            Email: <input type="text" name="email" placeholder="enter your email"/><br/>
            Password: <input type="password" name="pwd" placeholder="enter your password"/><br/>
            <input type="submit" value="send"/>
        </p>
    </form>
	
</body>
</html>