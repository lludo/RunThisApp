<?php



class Membership {
	
	static function validate($entityManager, $email, $pwd) {
                
                
                $developer = $entityManager
                        ->getRepository('Entities\Developer')
                        ->findOneBy(array('email' => $email, 'password' => sha1($pwd)));
		
		if($developer != NULL) {
			$_SESSION['developerId'] = $developer->getId();
			$_SESSION['developerName'] = $developer->getName();
			return TRUE;
		} else {
                    return FALSE;
                }
		
	} 
	
	static function logout() {
		if(isset($_SESSION['developerId'])) {
			unset($_SESSION['developerId']);
			unset($_SESSION['developerName']);
			
			if(isset($_COOKIE[session_name()])) 
				setcookie(session_name(), '', time() - 1000);
				session_destroy();
		}
	}
	
	static function isLoggedIn() {
		return (isset($_SESSION['developerId']));
	}
	
}
?>
