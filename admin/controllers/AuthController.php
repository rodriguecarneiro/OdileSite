<?php

class AuthController extends \Core\Controller
{

	public function indexAction() {

		$this->render("index");
	}

	public function loginAction() {

		//get post
		$email = $this->getPOST('login');
		$password = $this->getPOST('password');

		//check login
		if(!$userDetails = $this->model->checkLogin($email, $password)){
		   $this->redirect('/admin/auth');
		}
		      
		//save user details in $_SESSION
	    foreach ($userDetails as $field => $value) {
	    	$_SESSION['user'][$field] = $value;
	    }

		$this->redirect('/admin');
	}

	public function logoutAction() {
		session_destroy();
		$this->redirect('/');
	}


	public function ajax(){	

	}
}