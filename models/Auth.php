<?php

namespace Models;

use PDO;

Class Auth extends \Core\Crud{
   	
   	/**
   	 * check email and password pair 
   	 * @param  string $email    
   	 * @param  string $password 
   	 * @return Object | false           
   	 */
	public function checklogin($login = "", $password = ""){

		// select db_password from the email
		// return db_password | false
		$user = current($this->select([
			'table' 	=> 'user',
			'where'		=> array('login' => $login),
			'fetchMode' => PDO::FETCH_ASSOC
		]));

		//check email and password pair  
		if (!empty($user)) {
			if (crypt($password, $user['password']) == $user['password']){
				return $user;
			}
		}

		return false;
	}
}