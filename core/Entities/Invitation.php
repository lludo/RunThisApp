<?php

namespace Entities;

/**
 * @Entity @Table(name="invitation")
 */
class Invitation {
	
	/**
     * @Id @Column(type="integer") @GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @Column(type="string", length=150)
     */
    private $subject;
    
    /**
     * @Column(type="string", length=250)
     */
    private $text;
    
    /**
     * @Column(type="string", length=50)
     */
    private $token;
    
    /**
     * @Column(type="datetime", name="date_sent")
     */
    private $dateSent;
    
    /**
     * @Column(type="string", length=50)
     */
    private $status;
    
    /**
     * @ManyToOne(targetEntity="Developer", inversedBy="invitations")
     */
    private $developer;
    
    /**
	 * @OneToOne(targetEntity="Device")
	 * @JoinColumn(name="id_device", referencedColumnName="id")
	 */
    private $device;
    
	/**
     * @ManyToOne(targetEntity="Tester", inversedBy="invitations")
     */
    private $tester;
    
    public function setDeveloper($developer) {
    	if ($this->developer !== $developer) {
        	$developer->addInvitation($this);
        	$this->developer = $developer;
        }
    }
    
    public function getDeveloper() {
        return $this->developer;
    }
    
    public function setDevice($device) {
    	if ($this->device !== $device) {
        	$device->addInvitation($this);
        	$this->device = $device;
        }
    }
    
    public function getDevice() {
        return $this->device;
    }
    
    public function setTester($tester) {
    	if ($this->tester !== $tester) {
        	$tester->addInvitation($this);
        	$this->tester = $tester;
        }
    }
    
    public function getTester() {
        return $this->tester;
    }
}

?>
