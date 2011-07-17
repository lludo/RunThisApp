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

require_once __DIR__ . '/tools.php';
require_once __DIR__ . '/core/Membership.php';

if (Membership::isLoggedIn()) {
    $userName = $_SESSION['developerName'];
} else {
    $userName = 'Guest';
}


//header can be included from root folder or back subfolder.
$base = Tools::current_url();
if (strcasecmp(basename(dirname($base)), 'back') == 0) {
    $rootPath = '../';
    $backPath = '';
} else {
    $rootPath = '';
    $backPath = 'back/';    
}


//header domain management
$currentDomain = '';
$currentPage = basename(Tools::current_url());

$invitationPages = array('invitation_list.php', 'invitation_send.php', 'invitation_new.php');
$testerPages = array('tester_list.php', 'tester_create.php', 'tester_new.php');
$applicationPages = array('application_list.php', 'application_new.php', 'application_upload.php');

if (in_array($currentPage, $invitationPages)) {
    $currentDomain = 'invitation';
} else if (in_array($currentPage, $testerPages)) {
    $currentDomain = 'tester';
} else if (in_array($currentPage, $applicationPages)) {
    $currentDomain = 'application';
}

?>

<div id="header">
    <h2><a href="<?php echo $rootPath; ?>">Run This App</a></h2>

    <ul class="menu">
        <li class="invitations<?php if ($currentDomain=='invitation') echo ' active'; ?>"><a href="<?php echo $backPath; ?>invitation_list.php">Invitations</a></li>
        <li class="testers<?php if ($currentDomain=='tester') echo ' active'; ?>"><a href="<?php echo $backPath; ?>tester_list.php">Testers</a></li>
        <li class="applications<?php if ($currentDomain=='application') echo ' active'; ?>"><a href="<?php echo $backPath; ?>application_list.php">Applications</a></li>
    </ul>

    <ul class="login">
        <li>Hi, <?php echo $userName; ?></li>
        
<?php if (Membership::isLoggedIn()) { ?>
        <li><a href="<?php echo $backPath; ?>developer_profile.php">My profile</a></li>
        <li><a href="<?php echo $rootPath; ?>login.php?action=logout">Log Out</a></li>
  
<?php } else { ?>
        <li><a href="<?php echo $rootPath; ?>register.php">Register</a></li>	
        <li><a href="<?php echo $rootPath; ?>login.php">Log In</a></li>	
<?php } ?>
        
    </ul>
</div>
