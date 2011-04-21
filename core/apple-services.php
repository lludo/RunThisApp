<?php

require_once __DIR__ . '/../lib/cfpropertylist/CFPropertyList.php';
require_once __DIR__ . '/../credentials.php';
require_once __DIR__ . '/tools.php';

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
     * Apple client id
     * @var string $client_id
     */
    private static $client_id = 'XABBG36SBA';
    
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

        $url = 'https://daw2.apple.com/cgi-bin/WebObjects/DSAuthWeb.woa/wa/' .
            'clientDAW?format=plist&appIdKey=D136F3CA19FC87ADBC8514E10325B1000184218304C8DB66713C0CB40291F620' .
            '&appleId=' . urlencode($user) . '&password=' . urlencode($pwd) . '&userLocale=en_US&protocolVersion=A1234';

        // create a new cURL resource
        $ch = curl_init();

        // set URL and other appropriate options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'User-Agent: Xcode',
            'Accept: text/x-xml-plist',
            'Accept-Language: en-us',
            'Connection: keep-alive'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
        
        //TODO do not use a file
        //curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');
        //curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
        curl_setopt($ch, CURLOPT_HEADER, true);
        preg_match('/^set-cookie: (.*?)$/m', curl_exec($ch), $m);
        self::$cookie = $m[1];
        
        $data = curl_exec($ch);
        
        // close cURL resource
        curl_close($ch);
        
        //TODO: we cant read this because we have CURLOPT_HEADER for the cookie
        //$plist = new CFPropertyList();
        //$plist->parse($data, CFPropertyList::FORMAT_AUTO);
        //$plistData = $plist->toArray();

        return $data; //$plistData;
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
        $payload['protocolVersion'] = 'GL9N1P';
        $payload['requestId'] = Tools::randomAppleRequestId();

        $user_locale = array();
        $user_locale[0] = 'en_US';
        $payload['userLocale'] = $user_locale;

        $plist = new CFPropertyList();
        $type_detector = new CFTypeDetector();
        $plist->add($type_detector->toCFType($payload));
        $contents = $plist->toXML(true);

        // create a new cURL resource
        $ch = curl_init();

        // set URL and other appropriate options
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

        //TODO do not use a file
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_COOKIE, self::$cookie);
        preg_match('/^Set-Cookie: (.*?)$/m', curl_exec($ch), $m);
        self:$cookie = $m[1];
        //curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');
        //curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');

        //TODO secure the connection
        //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        //curl_setopt($ch, CURLOPT_CAINFO, __DIR__ . '/certs/VeriSignCA.pem');

        $data = curl_exec($ch);

        // close cURL resource
        curl_close($ch);
        
        //TODO: we cant read this because we have CURLOPT_HEADER for the cookie
        //$plist = new CFPropertyList();
        //$plist->parse($data, CFPropertyList::FORMAT_AUTO);
        //$plistData = $plist->toArray();

        return $data;//$plistData;
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
        $payload['protocolVersion'] = 'GL9N1P';
        $payload['requestId'] = Tools::randomAppleRequestId();

        $user_locale = array();
        $user_locale[0] = 'en_US';
        $payload['userLocale'] = $user_locale;

        $plist = new CFPropertyList();
        $type_detector = new CFTypeDetector();
        $plist->add($type_detector->toCFType($payload));
        $contents = $plist->toXML(true);

        // create a new cURL resource
        $ch = curl_init();

        // set URL and other appropriate options
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
        
        $data = curl_exec($ch);
        
        // close cURL resource
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
        $url = self::$base_url_services . 'listDevices.action?clientId=' .  self::$client_id;

        // Generating  content
        $payload = array();

        $payload['clientId'] = self::$client_id;
        $payload['protocolVersion'] = 'GL9N1P';
        $payload['requestId'] = Tools::randomAppleRequestId();
        $payload['teamId'] = $team_id;

        $user_locale = array();
        $user_locale[0] = 'en_US';
        $payload['userLocale'] = $user_locale;

        $plist = new CFPropertyList();
        $type_detector = new CFTypeDetector();
        $plist->add($type_detector->toCFType($payload));
        $contents = $plist->toXML(true);

        // create a new cURL resource
        $ch = curl_init();

        // set URL and other appropriate options
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

        $data = curl_exec($ch);
        
        // close cURL resource
        curl_close($ch);

        $plist = new CFPropertyList();
        $plist->parse($data, CFPropertyList::FORMAT_AUTO);
        $plistData = $plist->toArray();

        return $plistData;
    }

    public static function addDevice() {

        //TODO: xxx
    }

    public static function updateDevice($deviceId) {

        //TODO: xxx
    }

    public static function listAppIds() {

        //TODO: xxx
    }

    public static function viewAppId() {

        //TODO: xxx
    }

    public static function addAppId() {

        //TODO: xxx
    }

    public static function deleteAppId() {

        //TODO: xxx
    }

    public static function listMyDevelopmentCertRequests() {

        //TODO: xxx
    }

    public static function downloadDevelopmentCert() {

        //TODO: xxx
    }

    public static function revokeDevelopmentCert() {

        //TODO: xxx
    }

    public static function submitDevelopmentCSR() {

        //TODO: xxx
    }

    public static function downloadTeamProvisioningProfile() {

        //TODO: xxx
    }

    public static function listProvisioningProfiles() {

        //TODO: xxx
    }

    public static function downloadProvisioningProfile() {

        //TODO: xxx
    }

    public static function listDistributionCertRequests() {

        //TODO: xxx
    }

    public static function downloadDistributionCert() {

        //TODO: xxx
    }

    public static function revokeDistributionCert() {

        //TODO: xxx
    }

    public static function submitDistributionCSR() {

        //TODO: xxx
    }
}

?>
