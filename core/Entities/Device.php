<?php

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