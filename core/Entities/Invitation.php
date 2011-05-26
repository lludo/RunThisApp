<?php

namespace Entities;

/**
 * @Entity @Table(name="invitation")
 */
class Invitation {
    
    const STATUS_SENT = 'sent';
    const STATUS_UDID = 'udid';
    const STATUS_PROFILE = 'profile';
    const STATUS_INSTALLED = 'installed';
    
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
    
    /**
     * @ManyToOne(targetEntity="Version", inversedBy="invitations")
     */
    private $version;
    
    public function setSubject($subject) {
        $this->subject = $subject;
    }
    
    public function getSubject() {
        return $this->subject;
    }
    
    public function setText($text) {
        $this->text = $text;
    }
    
    public function getText() {
        return $this->text;
    }
    
    public function setToken($token) {
        $this->token = $token;
    }
    
    public function getToken() {
        return $this->token;
    }
    
    public function setDateSent($dateSent) {
        $this->dateSent = $dateSent;
    }
    
    public function getDateSent() {
        return $this->dateSent;
    }
    
    public function setStatus($status) {
        $this->status = $status;
    }
    
    public function getStatus() {
        return $this->status;
    }
    
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
        	$device->setInvitation($this);
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
    
    public function setVersion($version) {
    	if ($this->version !== $version) {
        	$version->addInvitation($this);
        	$this->version = $version;
        }
    }
    
    public function getVersion() {
        return $this->version;
    }
}

?>
