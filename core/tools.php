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
}

?>
