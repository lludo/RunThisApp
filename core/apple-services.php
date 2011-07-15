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

require_once __DIR__ . '/../lib/cfpropertylist/CFPropertyList.php';
require_once __DIR__ . '/../credentials.php';
require_once __DIR__ . '/../tools.php';

/**
 * AppleServices provides mothodes to access developer rovisioning portal
 */
class AppleServices {
    
    /**
     * Apple web services base URL
     * @var string $base_url_services
     */
    private static $base_url_services = 'https://connect1.apple.com/services/GL9N1P/';
    
    /**
     * Apple protocol version for daw2 server
     * @var string $protocol_version_daw2
     */
    private static $protocol_version_daw2 = 'A1234';
    
    /**
     * Apple protocol version for connerct1 server
     * @var string $protocol_version_connect1
     */
    private static $protocol_version_connect1 = 'GL9N1P';
    
    /**
     * Apple app id key
     * @var string $app_id_key
     */
    private static $app_id_key = 'D136F3CA19FC87ADBC8514E10325B1000184218304C8DB66713C0CB40291F620';
    
    /**
     * Apple client id
     * @var string $client_id
     */
    private static $client_id = 'XABBG36SBA';
    
    /**
     * Apple user locale
     * @var string $user_locale
     */
    private static $user_locale = 'en_US';
    
    /**
     * User used for authentication
     * @var string $user
     */
    private static $user = NULL;
    
    /**
     * Password used for authentication
     * @var string $pwd
     */
    private static $passwod = NULL;
    
    /**
     * Coookie used for authentication
     * @var string $cookie
     */
    private static $cookie;
    
    /**
     * Connect the developer with its user name/password and store the connection cookie
     * @param string $user
     * @param string $pwd 
     * 
     * if $result['resultCode'] equal 0 you are connected
     * user info are acessible via $result['firstName'] and $result['lastName']
     * 
     * else you can print the error $result['resultString']
     * 
     * @return array $result
     */
    public static function connect($user, $pwd) {
        
        self::$user = $user;
        self::$passwod = $pwd;
        
        $url = 'https://daw2.apple.com/cgi-bin/WebObjects/DSAuthWeb.woa/wa/clientDAW?format=plist&appIdKey='. self::$app_id_key . 
                    '&appleId=' . urlencode($user) . '&password=' . urlencode($pwd) . 
                    '&userLocale=' . self::$user_locale . '&protocolVersion=' . self::$protocol_version_daw2;

        // Create a new cURL resource
        $ch = curl_init();

        // Set URL and other appropriate options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'User-Agent: Xcode',
            'Accept: text/x-xml-plist',
            'Accept-Language: en-us',
            'Connection: keep-alive'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
        curl_setopt($ch, CURLOPT_HEADER, true);
        
        // Execute and close cURL
        $data = curl_exec($ch);
        curl_close($ch);
        
        self::$cookie = AppleServices::get_cookie($data);
        $content = AppleServices::get_content($data);
        
        $plist = new CFPropertyList();
        $plist->parse($content, CFPropertyList::FORMAT_AUTO);
        $plistData = $plist->toArray();

        return $plistData;
    }
    
    /**
     * Get developer detail informations
     * 
     * If $result['resultCode'] equals 0 everything is good
     * info returned are $result['developer.email'], $result['developer.firstName'] and $result['developer.lastName']
     * 
     * else you can print the error $result['resultString']
     * 
     * @return array $result
     */
    public static function viewDeveloper() {

        // Create Web Service URL
        $url = self::$base_url_services . 'viewDeveloper.action?clientId=' . self::$client_id;

        // Generating  content
        $payload = array();

        $payload['clientId'] = self::$client_id;
        $payload['protocolVersion'] = self::$protocol_version_connect1;
        $payload['requestId'] = Tools::randomAppleRequestId();

        $user_locale = array();
        $user_locale[0] = self::$user_locale;
        $payload['userLocale'] = $user_locale;

        $plist = new CFPropertyList();
        $type_detector = new CFTypeDetector();
        $plist->add($type_detector->toCFType($payload));
        $contents = $plist->toXML(true);

        // Create a new cURL resource
        $ch = curl_init();

        // Set URL and other appropriate options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $contents);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'User-Agent: Xcode',
            'Content-Type: text/x-xml-plist',
            'Accept: text/x-xml-plist',
            'Accept-Language: en-us',
            'Connection: keep-alive'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
        curl_setopt($ch, CURLOPT_COOKIE, self::$cookie);
        
        // Execute and close cURL
        $data = curl_exec($ch);
        curl_close($ch);
        
        $plist = new CFPropertyList();
        $plist->parse($data, CFPropertyList::FORMAT_AUTO);
        $plistData = $plist->toArray();

        return $plistData;
    }

    /**
     * Get all developer teams
     * 
     * If $result['resultCode'] equals 0 everything is good
     * info returned is $result['teamId'] and $result['name']
     * 
     * else you can print the error $result['resultString']
     * 
     * @return array $result
     */
    public static function listTeams() {

        // Create Web Service URL
        $url = self::$base_url_services . 'listTeams.action?clientId=' . self::$client_id;

        // Generating  content
        $payload = array();

        $payload['clientId'] = self::$client_id;
        $payload['protocolVersion'] = self::$protocol_version_connect1;
        $payload['requestId'] = Tools::randomAppleRequestId();

        $user_locale = array();
        $user_locale[0] = self::$user_locale;
        $payload['userLocale'] = $user_locale;

        $plist = new CFPropertyList();
        $type_detector = new CFTypeDetector();
        $plist->add($type_detector->toCFType($payload));
        $contents = $plist->toXML(true);

        // Create a new cURL resource
        $ch = curl_init();

        // Set URL and other appropriate options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $contents);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'User-Agent: Xcode',
            'Content-Type: text/x-xml-plist',
            'Accept: text/x-xml-plist',
            'Accept-Language: en-us',
            'Connection: keep-alive'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
        curl_setopt($ch, CURLOPT_COOKIE, self::$cookie);
        
        //TODO secure the connection
        //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        //curl_setopt($ch, CURLOPT_CAINFO, __DIR__ . '/certs/VeriSignCA.pem');
        
        // Execute and close cURL
        $data = curl_exec($ch);
        curl_close($ch);
        
        $plist = new CFPropertyList();
        $plist->parse($data, CFPropertyList::FORMAT_AUTO);
        $plistData = $plist->toArray();

        return $plistData;
    }

    /**
     * Get all developer devices for the team
     * 
     * if $result['resultCode'] equal 0 have your result
     * else you can print the error $result['resultString']
     * 
     * @param string $team_id
     * @return array $result
     */
    public static function listDevices($team_id) {

        // Create Web Service URL
        $url = self::$base_url_services . 'listDevices.action?clientId=' . self::$client_id;

        // Generating  content
        $payload = array();

        $payload['clientId'] = self::$client_id;
        $payload['protocolVersion'] = self::$protocol_version_connect1;
        $payload['requestId'] = Tools::randomAppleRequestId();
        $payload['teamId'] = $team_id;

        $user_locale = array();
        $user_locale[0] = self::$user_locale;
        $payload['userLocale'] = $user_locale;

        $plist = new CFPropertyList();
        $type_detector = new CFTypeDetector();
        $plist->add($type_detector->toCFType($payload));
        $contents = $plist->toXML(true);

        // Create a new cURL resource
        $ch = curl_init();

        // Set URL and other appropriate options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $contents);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'User-Agent: Xcode',
            'Content-Type: text/x-xml-plist',
            'Accept: text/x-xml-plist',
            'Accept-Language: en-us',
            'Connection: keep-alive'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
        curl_setopt($ch, CURLOPT_COOKIE, self::$cookie);

        //TODO secure the connection
        //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        //curl_setopt($ch, CURLOPT_CAINFO, __DIR__ . '/certs/VeriSignCA.pem');

        // Execute and close cURL
        $data = curl_exec($ch);
        curl_close($ch);
        
        $plist = new CFPropertyList();
        $plist->parse($data, CFPropertyList::FORMAT_AUTO);
        $plistData = $plist->toArray();

        return $plistData;
    }

    /**
     * Add a device
     * 
     * if $result['resultCode'] equal 0 have your result
     * else you can print the error $result['resultString']
     * 
     * @param string $device_number
     * @return array $result
     */
    //TODO: not curently working
    public static function addDevice($device_number, $name, $team_id) {

        // Create Web Service URL
        $url = self::$base_url_services . 'addDevice.action?clientId=' . self::$client_id;

        // Generating  content
        $payload = array();

        $payload['clientId'] = self::$client_id;
        $payload['deviceNumber'] = $device_number;
        $payload['name'] = $name;
        $payload['protocolVersion'] = self::$protocol_version_connect1;
        $payload['requestId'] = Tools::randomAppleRequestId();
        $payload['teamId'] = $team_id;
        
        $user_locale = array();
        $user_locale[0] = self::$user_locale;
        $payload['userLocale'] = $user_locale;

        $plist = new CFPropertyList();
        $type_detector = new CFTypeDetector();
        $plist->add($type_detector->toCFType($payload));
        $contents = $plist->toXML(true);
        
        // Create a new cURL resource
        $ch = curl_init();

        // Set URL and other appropriate options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $contents);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'User-Agent: Xcode',
            'Content-Type: text/x-xml-plist',
            'Accept: text/x-xml-plist',
            'Accept-Language: en-us',
            'Connection: keep-alive'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
        curl_setopt($ch, CURLOPT_COOKIE, self::$cookie);

        //TODO secure the connection
        //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        //curl_setopt($ch, CURLOPT_CAINFO, __DIR__ . '/certs/VeriSignCA.pem');

        // Execute and close cURL
        $data = curl_exec($ch);
        curl_close($ch);
        
        if ( strlen($data) != 0 ) {
            $plist = new CFPropertyList();
            $plist->parse($data, CFPropertyList::FORMAT_AUTO);
            $plistData = $plist->toArray();
        }
        
        return $plistData;
    }

    public static function updateDevice($deviceId) {

        //TODO: xxx
        return '<p>Function not implemented yet!</p>' . PHP_EOL;
    }
    
    /**
     * List application ids
     * 
     * if $result['resultCode'] equal 0 have your result
     * returned as an array in $result['appIds'], which
     * contains: appIdId, entitlements, prefix, identifier, name
     * 
     * else you can print the error $result['resultString']
     * 
     * @param string $team_id
     * @return array $result
     */
    public static function listAppIds($team_id) {

        // Create Web Service URL
        $url = self::$base_url_services . 'listAppIds.action?clientId=' . self::$client_id;
        
        // Generating  content
        $payload = array();

        $payload['clientId'] = self::$client_id;
        $payload['protocolVersion'] = self::$protocol_version_connect1;
        $payload['requestId'] = Tools::randomAppleRequestId();
        $payload['teamId'] = $team_id;
        
        $user_locale = array();
        $user_locale[0] = self::$user_locale;
        $payload['userLocale'] = $user_locale;

        $plist = new CFPropertyList();
        $type_detector = new CFTypeDetector();
        $plist->add($type_detector->toCFType($payload));
        $contents = $plist->toXML(true);

        // Create a new cURL resource
        $ch = curl_init();

        // Set URL and other appropriate options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $contents);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'User-Agent: Xcode',
            'Content-Type: text/x-xml-plist',
            'Accept: text/x-xml-plist',
            'Accept-Language: en-us',
            'Connection: keep-alive'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
        curl_setopt($ch, CURLOPT_COOKIE, self::$cookie);

        //TODO secure the connection
        //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        //curl_setopt($ch, CURLOPT_CAINFO, __DIR__ . '/certs/VeriSignCA.pem');

        // Execute and close cURL
        $data = curl_exec($ch);
        curl_close($ch);
        
        $plist = new CFPropertyList();
        $plist->parse($data, CFPropertyList::FORMAT_AUTO);
        $plistData = $plist->toArray();

        return $plistData;
    }

    public static function viewAppId() {

        //TODO: xxx
        return '<p>Function not implemented yet!</p>' . PHP_EOL;
    }

    public static function addAppId() {

        //TODO: xxx
        return '<p>Function not implemented yet!</p>' . PHP_EOL;
    }

    public static function deleteAppId() {

        //TODO: xxx
        return '<p>Function not implemented yet!</p>' . PHP_EOL;
    }

    public static function listMyDevelopmentCertRequests() {

        //TODO: xxx
        return '<p>Function not implemented yet!</p>' . PHP_EOL;
    }
    
    /**
     * Download development certificate
     * 
     * if $result['resultCode'] equal 0 have your result
     * returned is in the array $result['certificate']
     * values are: certContent and serialNumber
     * 
     * else you can print the error $result['resultString']
     * 
     * @param string $team_id
     * @return array $result
     */
    public static function downloadDevelopmentCert($team_id) {
        
        // Create Web Service URL
        $url = self::$base_url_services . 'downloadDevelopmentCert.action?clientId=' . self::$client_id;
        
        // Generating  content
        $payload = array();

        $payload['clientId'] = self::$client_id;
        $payload['protocolVersion'] = self::$protocol_version_connect1;
        $payload['requestId'] = Tools::randomAppleRequestId();
        $payload['teamId'] = $team_id;
        
        $user_locale = array();
        $user_locale[0] = self::$user_locale;
        $payload['userLocale'] = $user_locale;

        $plist = new CFPropertyList();
        $type_detector = new CFTypeDetector();
        $plist->add($type_detector->toCFType($payload));
        $contents = $plist->toXML(true);

        // Create a new cURL resource
        $ch = curl_init();

        // Set URL and other appropriate options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $contents);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'User-Agent: Xcode',
            'Content-Type: text/x-xml-plist',
            'Accept: text/x-xml-plist',
            'Accept-Language: en-us',
            'Connection: keep-alive'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
        curl_setopt($ch, CURLOPT_COOKIE, self::$cookie);

        //TODO secure the connection
        //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        //curl_setopt($ch, CURLOPT_CAINFO, __DIR__ . '/certs/VeriSignCA.pem');

        // Execute and close cURL
        $data = curl_exec($ch);
        curl_close($ch);
        
        $plist = new CFPropertyList();
        $plist->parse($data, CFPropertyList::FORMAT_AUTO);
        $plistData = $plist->toArray();

        return $plistData;
    }

    public static function revokeDevelopmentCert() {

        //TODO: xxx
        return '<p>Function not implemented yet!</p>' . PHP_EOL;
    }

    public static function submitDevelopmentCSR() {

        //TODO: xxx
        return '<p>Function not implemented yet!</p>' . PHP_EOL;
    }

    /**
     * Download team provisioning profile
     * 
     * if $result['resultCode'] equal 0 have your result
     * else you can print the error $result['resultString']
     * 
     * @param string $app_id_id
     * @param string $team_id
     * @return array $result
     */
    public static function downloadTeamProvisioningProfile($app_id_id, $team_id) {

        // Create Web Service URL
        $url = self::$base_url_services . 'downloadTeamProvisioningProfile.action?clientId=' . self::$client_id;

        // Generating  content
        $payload = array();

        $payload['appIdId'] = $app_id_id;
        $payload['clientId'] = self::$client_id;
        $payload['protocolVersion'] = self::$protocol_version_connect1;
        $payload['requestId'] = Tools::randomAppleRequestId();
        $payload['teamId'] = $team_id;
        
        $user_locale = array();
        $user_locale[0] = self::$user_locale;
        $payload['userLocale'] = $user_locale;

        $plist = new CFPropertyList();
        $type_detector = new CFTypeDetector();
        $plist->add($type_detector->toCFType($payload));
        $contents = $plist->toXML(true);

        // Create a new cURL resource
        $ch = curl_init();

        // Set URL and other appropriate options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $contents);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'User-Agent: Xcode',
            'Content-Type: text/x-xml-plist',
            'Accept: text/x-xml-plist',
            'Accept-Language: en-us',
            'Connection: keep-alive'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
        curl_setopt($ch, CURLOPT_COOKIE, self::$cookie);
        
        // Execute and close cURL
        $data = curl_exec($ch);
        curl_close($ch);
        
        $plist = new CFPropertyList();
        $plist->parse($data, CFPropertyList::FORMAT_AUTO);
        $plistData = $plist->toArray();

        return $plistData;
    }
    
    /**
     * List provisioning profiles
     * 
     * if $result['resultCode'] equal 0 have your result
     * else you can print the error $result['resultString']
     * 
     * @param string $team_id
     * @return array $result
     */
    public static function listProvisioningProfiles($team_id) {

        // Create Web Service URL
        $url = self::$base_url_services . 'listProvisioningProfiles.action?clientId=' . self::$client_id;

        // Generating  content
        $payload = array();
        
        $payload['clientId'] = self::$client_id;
        $payload['protocolVersion'] = self::$protocol_version_connect1;
        $payload['requestId'] = Tools::randomAppleRequestId();
        $payload['teamId'] = $team_id;
        
        $user_locale = array();
        $user_locale[0] = self::$user_locale;
        $payload['userLocale'] = $user_locale;

        $plist = new CFPropertyList();
        $type_detector = new CFTypeDetector();
        $plist->add($type_detector->toCFType($payload));
        $contents = $plist->toXML(true);

        // Create a new cURL resource
        $ch = curl_init();

        // Set URL and other appropriate options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $contents);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'User-Agent: Xcode',
            'Content-Type: text/x-xml-plist',
            'Accept: text/x-xml-plist',
            'Accept-Language: en-us',
            'Connection: keep-alive'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
        curl_setopt($ch, CURLOPT_COOKIE, self::$cookie);
        
        // Execute and close cURL
        $data = curl_exec($ch);
        curl_close($ch);
        
        $plist = new CFPropertyList();
        $plist->parse($data, CFPropertyList::FORMAT_AUTO);
        $plistData = $plist->toArray();

        return $plistData;
    }
    
    /**
     * Download provisioning profile
     * 
     * if $result['resultCode'] equal 0 have your result
     * returned profile in the string $result['encodedProfile']
     * 
     * else you can print the error $result['resultString']
     * 
     * @param $provisioning_profile_id
     * @param string $team_id
     * @return array $result
     */
    public static function downloadProvisioningProfile($provisioning_profile_id, $team_id) {

        // Create Web Service URL
        $url = self::$base_url_services . 'downloadProvisioningProfile.action?clientId=' . self::$client_id;

        // Generating  content
        $payload = array();
        
        $payload['clientId'] = self::$client_id;
        $payload['protocolVersion'] = self::$protocol_version_connect1;
        $payload['provisioningProfileId'] = $provisioning_profile_id;
        $payload['requestId'] = Tools::randomAppleRequestId();
        $payload['teamId'] = $team_id;
        
        $user_locale = array();
        $user_locale[0] = self::$user_locale;
        $payload['userLocale'] = $user_locale;

        $plist = new CFPropertyList();
        $type_detector = new CFTypeDetector();
        $plist->add($type_detector->toCFType($payload));
        $contents = $plist->toXML(true);

        // Create a new cURL resource
        $ch = curl_init();

        // Set URL and other appropriate options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $contents);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'User-Agent: Xcode',
            'Content-Type: text/x-xml-plist',
            'Accept: text/x-xml-plist',
            'Accept-Language: en-us',
            'Connection: keep-alive'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
        curl_setopt($ch, CURLOPT_COOKIE, self::$cookie);
        
        // Execute and close cURL
        $data = curl_exec($ch);
        curl_close($ch);
        
        $plist = new CFPropertyList();
        $plist->parse($data, CFPropertyList::FORMAT_AUTO);
        $plistData = $plist->toArray();

        return $plistData;
    }

    public static function listDistributionCertRequests() {

        //TODO: xxx
        return '<p>Function not implemented yet!</p>' . PHP_EOL;
    }

    /**
     * Download distribution certificate
     * 
     * if $result['resultCode'] equal 0 have your result
     * else you can print the error $result['resultString']
     * 
     * @param string $team_id
     * @return array $result
     */
    public static function downloadDistributionCert($team_id) {

        // Create Web Service URL
        $url = self::$base_url_services . 'downloadDistributionCert.action?clientId=' . self::$client_id;

        // Generating  content
        $payload = array();
        
        $payload['clientId'] = self::$client_id;
        $payload['protocolVersion'] = self::$protocol_version_connect1;
        $payload['requestId'] = Tools::randomAppleRequestId();
        $payload['teamId'] = $team_id;
        
        $user_locale = array();
        $user_locale[0] = self::$user_locale;
        $payload['userLocale'] = $user_locale;

        $plist = new CFPropertyList();
        $type_detector = new CFTypeDetector();
        $plist->add($type_detector->toCFType($payload));
        $contents = $plist->toXML(true);

        // Create a new cURL resource
        $ch = curl_init();

        // Set URL and other appropriate options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $contents);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'User-Agent: Xcode',
            'Content-Type: text/x-xml-plist',
            'Accept: text/x-xml-plist',
            'Accept-Language: en-us',
            'Connection: keep-alive'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
        curl_setopt($ch, CURLOPT_COOKIE, self::$cookie);
        
        // Execute and close cURL
        $data = curl_exec($ch);
        curl_close($ch);
        
        $plist = new CFPropertyList();
        $plist->parse($data, CFPropertyList::FORMAT_AUTO);
        $plistData = $plist->toArray();

        return $plistData;
    }

    public static function revokeDistributionCert() {

        //TODO: xxx
        return '<p>Function not implemented yet!</p>' . PHP_EOL;
    }

    public static function submitDistributionCSR() {

        //TODO: xxx
        return '<p>Function not implemented yet!</p>' . PHP_EOL;
    }
    
    /**
     * Edit certificate to add devices
     * 
     * Add the device udid to the provisioning profile, return
     * true if the device was added without error, else false.
     * 
     * @param string $provisioning_profile_id
     * @param string $device_number
     * @return boolean $result
     */
    public static function editProvisioningProfile($provisioning_profile_id, $device_number) {
        
        /**
         * NOTE: This is the only function we needed without having
         * WebServices, so we have to parse the site.
         * 
         * Step 1 connection to Apple Website
         *      1.1 get connetion urlfrom login page
         *      1.2 execute first part of login to get temp cookies
         *      1.3 execute last part to have a valid connetion cookie
         * 
         * Step 2 parse form (get inputs and selects data to modify profile)
         * 
         * Step 3 execute form action with data
         */
        
        $login_url = 'https://daw.apple.com/cgi-bin/WebObjects/DSAuthWeb.woa/wa/login?' . 
            'appIdKey=D635F5C417E087A3B9864DAC5D25920C4E9442C9339FA9277951628F0291F620&' . 
            'path=%2F%2Fdevcenter%2Fios%2Findex.action';
        
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        if ( $dom->loadHTMLFile($login_url) ) {
            
            $xpath = new DOMXPath($dom);
            $form = $xpath->query('//form[@name="appleConnectForm"]')->item(0);
            
            $login_post_url = $form->getAttribute('action');
        }
        else {
            $errors = libxml_get_errors();
            echo display_xml_errors($errors, $login_url);
            libxml_clear_errors();
            return FALSE;
        }
        
        $login_post_url = 'https://daw.apple.com' . $login_post_url;
        $contents = 'theAccountName=' . self::$user . '&theAccountPW=' . self::$passwod;
        
        $ch = curl_init();

        // Set URL and other appropriate options
        curl_setopt($ch, CURLOPT_URL, $login_post_url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $contents);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'User-Agent: Mozilla/5.0  Firefox/4.0',
            'Accept: */*',
            'Accept-Language: en-us',
            'Connection: keep-alive'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
        curl_setopt($ch, CURLOPT_HEADER, true);
        
        // Execute and close cURL
        $data = curl_exec($ch);
        curl_close($ch);
        
        foreach(preg_split("/(\r?\n)/", $data) as $line){
            if ( preg_match('/^Set-Cookie: (.*?)$/mi', $line, $matches) ) {
                $cookies[] = $matches[1];
            }
        }

        
        
        $url = 'https://developer.apple.com/devcenter/ios/index.action';
        $ch = curl_init();
        
        // Set URL and other appropriate options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
        foreach ($cookies as $cookie) {
            $values = split(';', $cookie);
            curl_setopt($ch, CURLOPT_COOKIE, $values[0]);
        }
        curl_setopt($ch, CURLOPT_HEADER, true);
        
        // Execute and close cURL
        $data = curl_exec($ch);
        curl_close($ch);
        
        foreach(preg_split("/(\r?\n)/", $data) as $line){
            if ( preg_match('/^Set-Cookie: (.*?)$/mi', $line, $matches) ) {
                $cookies[] = $matches[1];
            }
        }
        
        
        
        $edit_url = 'https://developer.apple.com/ios/manage/provisioningprofiles/edit.action?provDisplayId=' . $provisioning_profile_id;
        $ch = curl_init();
        
        // Set URL and other appropriate options
        curl_setopt($ch, CURLOPT_URL, $edit_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
        $totalCookie = "";
        foreach ($cookies as $cookie) {
            $values = split(';', $cookie);
            $totalCookie .= $values[0] . "; ";
        }
        curl_setopt($ch, CURLOPT_COOKIE, $totalCookie);
        curl_setopt($ch, CURLOPT_HEADER, true);
        
        // Execute and close cURL
        $data = curl_exec($ch);
        curl_close($ch);

        
        
        //TODO: parse only inputs and select in the form named "save"
        
        // Load dom the page
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        if ( $dom->loadHTML($data) ) {

            $xpath = new DOMXPath($dom);
            $selects = $xpath->query('//select');

            // Parse all select and retrieve selected value
            $keysValuesArray = NULL;
            foreach ($selects as $select) {

                    $select_dom = new DOMDocument('1.0', 'utf-8');
                    $select_dom->loadHTML('<html></html>');
                    $select_dom->documentElement->appendChild($select_dom->importNode($select, true));

                    $xpath_select = new DOMXPath($select_dom);
                    $option_selected = $xpath_select->query('//option[@selected="selected"]')->item(0);

                    $key_value = $select->getAttribute('name') . '=' . $option_selected->getAttribute('value');
                    $keysValuesArray[$key_value] = $key_value;
            }

            // Parse all input and retrieve values (and add the UDID we want for this profile)
            $inputs = $xpath->query('//input');

            foreach ($inputs as $input) {

                if ( $input->getAttribute('type') == 'checkbox' ) {

                    if ( ($input->getAttribute('checked') == 'checked') || ($input->getAttribute('value') == $device_number) ) {
                        $key_value = $input->getAttribute('name') . '=' . $input->getAttribute('value');
                        $keysValuesArray[$key_value] = $key_value;
                    }
                }
                else if ( ($input->getAttribute('type') != 'submit') && ($input->getAttribute('type') != 'image') ) {
                    $key_value = $input->getAttribute('name') . '=' . $input->getAttribute('value');
                    $keysValuesArray[$key_value] = $key_value;
                }
            }
            $keysValuesArray[] = 'submit.x=0';
            $keysValuesArray[] = 'submit.y=0';



            //TODO: work in progress... (tmp: display url, data,...)
            $form = $xpath->query('//form[@name="save"]')->item(0);
            
            $edit_post_url = 'https://developer.apple.com' . $form->getAttribute('action');
            
            $contents = "";
            foreach ($keysValuesArray as $keyValue) {
                $contents .= '&' . str_replace(' ', '+', $keyValue);
            }
        }
        else {
			$errors = libxml_get_errors();
			echo display_xml_errors($errors, $login_url);
			libxml_clear_errors();
            return FALSE;
        }
        
        $ch = curl_init();

        // Set URL and other appropriate options
        curl_setopt($ch, CURLOPT_URL, $edit_post_url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $contents);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
        $totalCookie = "";
        foreach ($cookies as $cookie) {
            $values = split(';', $cookie);
            $totalCookie .= $values[0] . "; ";
        }
        curl_setopt($ch, CURLOPT_COOKIE, $totalCookie);
        curl_setopt($ch, CURLOPT_HEADER, true);
        
        // Execute and close cURL
        $data = curl_exec($ch);
        curl_close($ch);
        
        
        
        $response = preg_split("/(\r?\n)/", $data);
        
        if ( $response[0] == 'HTTP/1.1 100 Continue' ) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
    
    /**
     * Return the cookie to set from the raw curl content
     * 
     * @param string $data
     * @return string $cookie
     */
    private static function get_cookie($data) {
        preg_match('/^Set-Cookie: (.*?)$/mi', $data, $matches);
        return $matches[1];
    }
    
    /**
     * Return the response content from the raw curl content
     * 
     * @param string $data
     * @return string $content
     */
    private static function get_content($data) {
        $pos = strpos($data, '<?xml version="1.0" encoding="UTF-8"?>');
        return substr($data, $pos);
    }
}

?>
