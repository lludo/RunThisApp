<?php

namespace Entities;

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
     * @OneToMany(targetEntity="Invitation", mappedBy="version")
     */
    private $invitations = null;
    
    public function __construct() {
        $this->invitations = new ArrayCollection();
    }
    
    public function setApplication($application) {
    	if ($this->application !== $application) {
        	$application->addVersion($this);
        	$this->application = $application;
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
}

?>
