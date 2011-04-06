<?php

namespace Entities;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity @Table(name="tester")
 */
class Tester {
	
	/**
     * @Id @Column(type="integer") @GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @Column(type="string", length=50, unique=true)
     */
    private $email;
    
    /**
     * @Column(type="string", length=50)
     */
    private $name;
    
    /**
     * @Column(type="string", length=250, nullable=true)
     */
    private $text;
    
    /**
	 * @ManyToMany(targetEntity="Application", mappedBy="testers")
	 */
    private $applications = null;
    
    /**
     * @OneToMany(targetEntity="Device", mappedBy="tester")
     */
    private $devices = null;
    
    /**
     * @OneToMany(targetEntity="Invitation", mappedBy="tester")
     */
    private $invitations = null;
    
    /**
     * @ManyToOne(targetEntity="Developer", inversedBy="testers")
     */
    private $developer;
    
    public function __construct() {
        $this->applications = new ArrayCollection();
        $this->devices = new ArrayCollection();
        $this->invitations = new ArrayCollection();
    }
    
    public function setName($name) {
    	$this->name = $name;
    }
    
    public function getName() {
    	return $this->name;
    }
    
    public function addApplication($application) {
        $this->applications[] = $application;
    }
    
    public function getApplications() {
        return $this->applications;
    }
   	
    public function addDevice($device) {
        $this->devices[] = $device;
    }
    
    public function getDevices() {
        return $this->devices;
    }
    
    public function addInvitation($invitation) {
        $this->invitations[] = $invitation;
    }
    
    public function getInvitations() {
       return $this->invitations;
    }
    
    public function setDeveloper($developer) {
    	if ($this->developer !== $developer) {
        	$developer->addTester($this);
        	$this->developer = $developer;
        }
    }
    
    public function getDeveloper() {
        return $this->developer;
    }
}

?>
