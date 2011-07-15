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

/**
 * Description of tools
 *
 * @author Ludovic Landry et pis aussi un peu pcans :p
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
            
        $dir = __DIR__ . '/app/' . $token . '/app_bundle/Payload/';
        
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
	
    static function rel2abs($rel, $base) {
        
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
    
    static function dieError($msg) {
    	if (!headers_sent()) {
    		header('HTTP/1.0 500 Internal Server Error');
    	}
    	die('Error: ' . $msg);
    }
}

function display_xml_errors($error, $xml)
{
    $return  = $xml[$error->line - 1] . "\n";
    $return .= str_repeat('-', $error->column) . "^\n";

    switch ($error->level) {
        case LIBXML_ERR_WARNING:
            $return .= "Warning $error->code: ";
            break;
         case LIBXML_ERR_ERROR:
            $return .= "Error $error->code: ";
            break;
        case LIBXML_ERR_FATAL:
            $return .= "Fatal Error $error->code: ";
            break;
    }

    $return .= trim($error->message) .
               "\n  Line: $error->line" .
               "\n  Column: $error->column";

    if ($error->file) {
        $return .= "\n  File: $error->file";
    }

    return "$return\n\n--------------------------------------------\n\n";
}
?>
