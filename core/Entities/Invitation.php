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
     * @ManyToOne(targetEntity="Device", inversedBy="invitations")
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
            $this->developer = $developer;
            $developer->addInvitation($this);
        }
    }
    
    public function getDeveloper() {
        return $this->developer;
    }
    
    public function setDevice($device) {
    	if ($this->device !== $device) {
            $this->device = $device;
            $device->addInvitation($this);
        }
    }
    
    public function getDevice() {
        return $this->device;
    }
    
    public function setTester($tester) {
    	if ($this->tester !== $tester) {
            $this->tester = $tester;
            $tester->addInvitation($this);
        }
    }
    
    public function getTester() {
        return $this->tester;
    }
    
    public function setVersion($version) {
    	if ($this->version !== $version) {
            $this->version = $version;
            $version->addInvitation($this);
        }
    }
    
    public function getVersion() {
        return $this->version;
    }
}

?>
