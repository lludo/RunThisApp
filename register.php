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
	<title>Run This App | Register</title>
	<link href="css/style-0001.css" media="screen" type="text/css" rel="stylesheet">
</head>
<body>
    
    <?php include('header.php'); ?>
    
    <form method="POST" action="back/developer_create.php">
        <p>
            Display Name: <input type="text" name="name" placeholder="enter your display name"/><br/>
            Email: <input type="text" name="email" placeholder="enter your email" /><br/>
            Password: <input type="password" name="pwd" placeholder="enter a password" /><br/>
            <input type="submit" value="send" />
	</p>
    </form>
	
</body>
</html>