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
 * @Entity @Table(name="version")
 */
class Version {
	
	/**
     * @Id @Column(type="integer") @GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @Column(type="string", length=50)
     */
    private $version;
    
    /**
     * @Column(type="string", length=250, nullable=true)
     */
    private $text;
    
    /**
     * @Column(type="datetime", name="date_upload")
     */
    private $dateUpload;
    
    /**
     * @ManyToOne(targetEntity="Application", inversedBy="versions")
     */
    private $application;
    
    /**
     * @Column(type="string", length=50, nullable=true)
     */
    private $token;
    
    /**
     * @Column(type="string", length=50, nullable=true)
     */
    private $name;

    /**
     * @OneToMany(targetEntity="Invitation", mappedBy="version")
     */
    private $invitations = null;
    
    public function __construct() {
        $this->invitations = new ArrayCollection();
    }
    
    public function setApplication($application) {
    	if ($this->application !== $application) {
            $this->application = $application;
            $application->addVersion($this);
        }
    }
    
    public function getApplication() {
        return $this->application;
    }
    
    public function addInvitation($invitation) {
        $this->invitations[] = $invitation;
    }
    
    public function getInvitations() {
       return $this->invitations;
    }
    
    public function getVersion() {
        return $this->version;
    }
	
    public function setVersion($version) {
        $this->version = $version;
    }
	
    public function getId() {
        return $this->id;
    }
	
    public function setId($id) {
        $this->id = $id;
    }
    
    public function setDateUpload($dateUpload) {
    	$this->dateUpload = $dateUpload;
    }
    
    public function getDateUpload() {
    	return $this->dateUpload;
    }
    
    public function getToken() {
    	return $this->token;
    }
    
    public function setToken($token) {
    	$this->token = $token;
    }
    
    public function getName() {
    	return $this->name;
    }
    
    public function setName($name) {
    	$this->name = $name;
    }
}

?>
