<?php

namespace Entities;

/**
 * @Entity @Table(name="device")
 */
class Device {
	
	/**
     * @Id @Column(type="integer") @GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @Column(type="string", length=50)
     */
    private $name;
    
    /**
     * @Column(type="datetime", name="date_creation")
     */
    private $dateCreation;
    
    /**
     * @Column(type="string", name="system_version", length=50)
     */
    private $systemVersion;
    
    /**
     * @Column(type="string", length=50)
     */
    private $model;
    
    /**
     * @Column(type="string", length=50, unique=true)
     */
    private $udid;
    
    /**
	 * @OneToOne(targetEntity="Invitation")
	 * @JoinColumn(name="id_invitation", referencedColumnName="id")
	 */
    private $invitation;
    
	/**
     * @ManyToOne(targetEntity="Tester", inversedBy="devices")
     */
    private $tester;
    
    public function setInvitation($invitation) {
    	if ($this->invitation !== $invitation) {
        	$invitation->addDevice($this);
        	$this->invitation = $invitation;
        }
    }
    
    public function getInvitation() {
        return $this->invitation;
    }
    
    public function setTester($tester) {
   		if ($this->tester !== $tester) {
        	$tester->addDevice($this);
        	$this->tester = $tester;
        }
    }
    
    public function getTester() {
        return $this->tester;
    }
}

?>