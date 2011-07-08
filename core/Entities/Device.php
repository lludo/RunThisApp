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
    * @OneToOne(targetEntity="Invitation")
    * @JoinColumn(name="id_invitation", referencedColumnName="id", nullable=true)
    */
    private $invitation;
    
    /**
     * @ManyToOne(targetEntity="Tester", inversedBy="devices")
     */
    private $tester;
    
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
    
    public function setInvitation($invitation) {
    	if ($this->invitation !== $invitation) {
            $this->invitation = $invitation;
            $invitation->setDevice($this);
        }
    }
    
    public function getInvitation() {
        return $this->invitation;
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