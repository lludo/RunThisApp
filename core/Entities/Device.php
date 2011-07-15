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
 * @Entity @Table(name="device")
 */
class Device {
	
    /**
     * @Id @Column(type="integer") @GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @Column(type="string", length=50, nullable=true)
     */
    private $name;
    
    /**
     * @Column(type="datetime", name="date_creation", nullable=true)
     */
    private $dateCreation;
    
    /**
     * @Column(type="string", name="system_version", length=50, nullable=true)
     */
    private $systemVersion;
    
    /**
     * @Column(type="string", length=50, nullable=true)
     */
    private $model;
    
    /**
     * @Column(type="string", length=50, nullable=true)
     */
    private $udid;
    
    /**
    * @OneToMany(targetEntity="Invitation", mappedBy="device")
    */
    private $invitations;
    
    /**
     * @ManyToOne(targetEntity="Tester", inversedBy="devices")
     */
    private $tester;
    
    public function __construct() {
        $this->invitations = new ArrayCollection();
    }
    
    public function setId($id) {
    	$this->id = $id;
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
    
    public function setDateCreation($dateCreation) {
    	$this->dateCreation = $dateCreation;
    }
    
    public function getDateCreation() {
    	return $this->dateCreation;
    }
    
    public function setSystemVersion($systemVersion) {
    	$this->systemVersion = $systemVersion;
    }
    
    public function getSystemVersion() {
    	return $this->systemVersion;
    }
       
    public function setModel($model) {
    	$this->model = $model;
    }
    
    public function getModel() {
    	return $this->model;
    }
    
    public function setUdid($udid) {
    	$this->udid = $udid;
    }
    
    public function getUdid() {
    	return $this->udid;
    }
    
    public function addInvitation($invitation) {
    	$this->invitations[] = $invitation;
    }
    
    public function getInvitations() {
        return $this->invitations;
    }
    
    public function setTester($tester) {
   	if ($this->tester !== $tester) {
            $this->tester = $tester;
            $tester->addDevice($this);
        }
    }
    
    public function getTester() {
        return $this->tester;
    }
}

?>