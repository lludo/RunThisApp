<?php

namespace Entities;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity @Table(name="application")
 */
class Application {
	
	/**
     * @Id @Column(type="integer") @GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @Column(type="string", length=50)
     */
    private $name;
    
    /**
     * @Column(type="string", name="icon_file", length=50, nullable=true)
     */
    private $iconFile;
    
    /**
     * @Column(type="string", name="bundle_id", length=50)
     */
    private $bundleId;
    
    /**
     * @Column(type="string", unique=true, length=250, nullable=true)
     */
    private $text;
    
    /**
	 * @ManyToMany(targetEntity="Tester", inversedBy="applications")
	 * @JoinTable(name="application_tester",
	 *      joinColumns={@JoinColumn(name="application_id", referencedColumnName="id")},
	 *      inverseJoinColumns={@JoinColumn(name="tester_id", referencedColumnName="id")}
	 *      )
	 */
    private $testers = null;
    
    /**
     * @OneToMany(targetEntity="Version", mappedBy="application")
     */
    private $versions = null;
	
	/**
     * @ManyToOne(targetEntity="Developer", inversedBy="applications")
     */
    private $developer;

    public function __construct() {
        $this->testers = new ArrayCollection();
        $this->versions = new ArrayCollection();
    }
    
    public function addTester($tester){
        $this->testers[] = $tester;
    }
    
    public function addVersion($version){
        $this->versions[] = $version;
    }
    
    public function setDeveloper($developer) {
    	if ($this->developer !== $developer) {
        	$developer->addApplication($this);
        	$this->developer = $developer;
        }
    }
    
    public function getDeveloper() {
        return $this->developer;
    }
}

?>