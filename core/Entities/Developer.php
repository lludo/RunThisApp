<?php

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
     * @Column(type="string", name="first_name", length=50)
     */
    private $firstName;
    
    /**
     * @Column(type="string", name="last_name", length=50)
     */
    private $lastName;
    
    /**
     * @Column(type="string", length=50, nullable=true)
     */
    private $login;
    
    /**
     * @Column(type="string", length=50, nullable=true)
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
    
    public function setFirstName($firstName) {
	$this->firstName = $firstName;
    }
    
    public function getFirstName() {
    	return $this->firstName;
    }
    
    public function setLastName($lastName) {
    	$this->lastName = $lastName;
    }
    
    public function getLastName() {
    	return $this->lastName;
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