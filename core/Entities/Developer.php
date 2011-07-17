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

namespace Entities;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity @Table(name="developer")
 */
class Developer {
	
	/**
     * @Id @Column(type="integer") @GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @Column(type="string", length=255)
     */
    private $name;
    
    /**
     * @Column(type="string", length=255)
     */
    private $email;
    
    /**
     * @Column(type="string", length=40)
     */
    private $password;
    
    /**
     * @OneToMany(targetEntity="Application", mappedBy="developer")
     */
    private $applications = null;
    
    /**
     * @OneToMany(targetEntity="Invitation", mappedBy="developer")
     */
    private $invitations = null;
    
    /**
     * @OneToMany(targetEntity="Tester", mappedBy="developer")
     */
    private $testers = null;
    
    public function __construct() {
        $this->applications = new ArrayCollection();
        $this->invitations = new ArrayCollection();
        $this->testers = new ArrayCollection();
    }
    
    public function getId() {
    	return $this->id;
    }
    
    public function setName($name) {
	$this->name = $name;
    }
    
    public function getName() {
    	return $this->name;
    }
    
    public function setEmail($email) {
	$this->email = $email;
    }
    
    public function getEmail() {
    	return $this->email;
    }
    
    public function setPassword($password) {
	$this->password = $password;
    }
    
    public function getPassword() {
    	return $this->password;
    }
    
    public function addApplication($application){
        $this->applications[] = $application;
    }
    
    public function addInvitation($invitation){
        $this->invitations[] = $invitation;
    }
    
    public function addTester($tester){
        $this->testers[] = $tester;
    }
}

?>