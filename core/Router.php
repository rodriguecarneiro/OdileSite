<?php

namespace Core;

class Router
{
	private $request;
	private $controller;
	private $method;
	private $params = array();

	/**
	 * Constructor
	 */
	public function __construct($admin_folder = 'admin') {

		$this->request = strtok($_SERVER['REQUEST_URI'], "?");

		//explode uri
		$tmp = explode('/', $this->request);
		$uri = array_splice($tmp, 1);

		//check if admin url
		$this->isAdmin = false;
		if($uri[0] == $admin_folder){
			//remove admin folder name from $uri
			$uri = array_splice($uri, 1);
			$this->isAdmin = true;
		}

		// set method
		if (isset($uri[1]) && is_numeric($uri[1])) {
			$method = "showAction";
			$params = array((int) $uri[1], array_slice($uri, 2));
		}
		else {
			$method = !empty($uri[1]) ? $uri[1] . "Action"  : "indexAction";
			$params = array_slice($uri, 2);
		}

		//control & set controller & method
		$this->controller = !empty($uri[0]) ? ucfirst($uri[0]) . 'Controller' : "ProjectController";
		
		$this->method = $method;
		$this->params = $params;
	}

	/**
	 * Return the controller to instanciate or the Closure to execute
	 * @return mixed
	 */
	public function getController() {
		return $this->controller;
	}
	/**
	 * Return the User submitted parameters through URI
	 * @return array
	 */
	public function getParams() {
		return $this->params;
	}
	/**
	 * Return the method to call
	 * @return String
	 */
	public function getMethod() {
		return $this->method;
	}

	public function getRequest() {
		return $this->request;
	}
}

