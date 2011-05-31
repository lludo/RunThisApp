<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of tools
 *
 * @author Ludovic Landry
 */
class Tools {
    
	/**
    * @return string current absolute uri 
     */
    static function current_url() {
		
		$server = $_SERVER['SERVER_NAME'];
		if ($_SERVER['SERVER_PORT'] != '80') {
			$server .= ':'.$_SERVER['SERVER_PORT'];
		}
		return 'http://' . $server . $_SERVER['REQUEST_URI'];
	}

    /**
     * Generate a random Apple request id used for web services
     * The format returned is XXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX
     * @return string request_id 
     */
    static function randomAppleRequestId() {
        
        $request_id = md5(rand());
        $request_id = substr_replace($request_id, '-', 20, 0);
        $request_id = substr_replace($request_id, '-', 16, 0);
        $request_id = substr_replace($request_id, '-', 12, 0);
        $request_id = substr_replace($request_id, '-', 8, 0);
        
        return strtoupper($request_id);
    }
    
    /**
     * Return an hexa hash generated randomly with the given length
     * @param int $length
     * @return string hash
     */
    static function randomHashWithSize($length) {
        return substr(md5(rand()), 0, $length); 
    }
    
    /**
     * Return the configurtion PLIST file for the application
     * identified by the token passed in parameter
     * @param string $token
     * @return string path
     */
    static function getInfoPlistPath($token) {
            
        $dir = __DIR__ . '/../app/' . $token . '/app_bundle/Payload/';
        
        $app_folder = NULL;
        if (is_dir($dir)) {
            if ( ($dh = opendir($dir)) ) {
                while (($file = readdir($dh)) !== false) {
                    if ( filetype($dir . $file) == 'dir' && $file != '.' && $file != '..' ) {
                        $app_folder = $file;
                        break;
                    }
                }
                closedir($dh);
            }
        }
        
        $plistPath = NULL;
        if ($app_folder != NULL) {
            
            $plistPath = $dir . $app_folder . '/';
            
            // We get the Info.plist of the app if it exists else <app_name>-Info.plist
            if ( file_exists($plistPath . 'Info.plist') ) {
                $plistFilePath = $plistPath . 'Info.plist';
            } else {
                $plistFilePath = $plistPath . $app_folder . '-Info.plist';
            }
            
        }
        
        return $plistFilePath;
    }
	
	static function rel2abs($rel, $base)
    {
        /* return if already absolute URL */
        if (parse_url($rel, PHP_URL_SCHEME) != '') return $rel;
        /* queries and anchors */
        if ($rel[0]=='#' || $rel[0]=='?') return $base.$rel;
        /* parse base URL and convert to local variables:
         $scheme, $host, $path */
        extract(parse_url($base));
        /* remove non-directory element from path */
        $path = preg_replace('#/[^/]*$#', '', $path);
        /* destroy path if relative url points to root */
        if ($rel[0] == '/') $path = '';
        /* dirty absolute URL */
        $abs = "$host$path/$rel";
        /* replace '//' or '/./' or '/foo/../' with '/' */
        $re = array('#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#');
        for($n=1; $n>0; $abs=preg_replace($re, '/', $abs, -1, $n)) {}
        /* absolute URL is ready! */
        return $scheme.'://'.$abs;
    }
}

?>
