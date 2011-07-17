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

require_once __DIR__ . '/../core/index.php';
require_once __DIR__ . '/../core/functions.php';
require_once __DIR__ . '/../tools.php';

if (!isset($_POST['name'], $_POST['email']) ) {
    die('parameters needed.'); 
}

$entityManager = initDoctrine();

$tester = $entityManager->getRepository('Entities\Tester')->findOneBy(array('email' => $_POST['email']));
if ($tester != NULL) {
    die('A tester with the email adress '.$_POST['email'].' already exists'); 
}
        
$tester = new Tester();
$tester->setName($_POST['name']);
$tester->setEmail($_POST['email']);
$entityManager->persist($tester);
$entityManager->flush();

//TODO link to dev

header('Location: tester_list.php');   

?>