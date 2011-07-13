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
 * @Entity @Table(name="application")
 */
class Application {
	
    /**
     * @Id @Column(type="integer") @GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @Column(type="string", length=50, nullable=true)
     */
    private $name;
    
    /**
     * @Column(type="string", name="icon_file", length=50, nullable=true)
     */
    private $iconFile;
    
    /**
     * @Column(type="string", name="bundle_id", length=50, nullable=true)
     */
    private $bundleId;
    
    /**
     * @Column(type="string", unique=true, length=250, nullable=true)
     */
    private $text;
    
    /**
     * @Column(type="string", name="token", length=50, nullable=true)
     */
    private $token;
    
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
    
    public function getName() {
    	return $this->name;
    }
    
    public function setName($name) {
    	$this->name = $name;
    }
    
    public function getIconFile() {
    	return $this->iconFile;
    }
    
    public function setIconFile($iconFile) {
    	$this->iconFile = $iconFile;
    }
    
    public function getToken() {
    	return $this->token;
    }
    
    public function setToken($token) {
    	$this->token = $token;
    }
    
    public function getBundleId() {
    	return $this->bundleId;
    }
    
    public function setBundleId($bundleId) {
    	$this->bundleId = $bundleId;
    }
    
    public function addTester($tester){
        $this->testers[] = $tester;
    }
    
    
    public function setDeveloper($developer) {
    	if ($this->developer !== $developer) {
            $this->developer = $developer;
            $developer->addApplication($this);
        }
    }
    
    public function getDeveloper() {
        return $this->developer;
    }
	
    public function addVersion($version){
        $this->versions[] = $version;
    }
    
    public function getVersions() {
	return $this->versions;
    }
}

?>