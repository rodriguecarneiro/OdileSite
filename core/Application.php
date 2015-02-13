<?php

namespace Core; 

class Application
{
	private $adminDir = 'admin';
	private $adminViewsDir;
	private $adminControllersDir;
	private $viewsDir;
	private $controllersDir;
	private $router;

	/**
	 * Constructor
	 */
	public function __construct() {	

		$this->setAdminViewsDir("../" . $this->adminDir . "/views/")
		->setAdminControllersDir("../" . $this->adminDir . "/controllers/")
		->setViewsDir("views/")
		->setControllersDir("../controllers/");

		spl_autoload_register(array($this, "autoload"));

		$this->router = new Router($this->adminDir);
	}
	/**
	 * Autoload for SPL. Think about putting your libraries and controller folder
	 * in include paths.
	 */
	public function autoload($className) {
        $pathToClass = str_replace("\\","/", $className);
        require($pathToClass . ".php");
    }
	/**
	 * Set the admin directory
	 * @param string $adminDir Directory where the admin is located
	 * @return \Core\Application
	 */
	public function setAdminDir($adminDir) {
		if(is_dir($adminDir)) {
			$this->adminDir = $adminDir;
		} else {
			throw new \Exception("$adminDir is not a valid directory");
		}
		return $this;
	}
	/**
	 * Set the admin controller directory
	 * @param string $adminControllersDir Directory where the controllers are located
	 * @return \Core\Application
	 */
	public function setAdminControllersDir($adminControllersDir) {
		if(is_dir($adminControllersDir)) {
			$this->adminControllersDir = $adminControllersDir;
		} else {
			throw new \Exception("$adminControllersDir is not a valid directory");
		}
		return $this;
	}
	/**
	 * Get the admin controller directory
	 * @return String
	 */
	public function getAdminControllersDir() {
		return $this->adminControllersDir;
	}
	/**
	 * Set the admin views directory
	 * @param string $adminDir Directory where the admin is located
	 * @return \Core\Application
	 */
	public function setAdminViewsDir($adminViewsDir) {
		if(is_dir($adminViewsDir)) {
			$this->adminViewsDir = $adminViewsDir;
		} else {
			throw new \Exception("$adminViewsDir is not a valid directory");
		}
		return $this;
	}
	/**
	 * Get the admin views directory
	 * @return String
	 */
	public function getAdminViewsDir() {
		return $this->adminViewsDir;
	}
	/**
	 * Set the controller directory
	 * @param string $controllerDir Directory where the controllers are located
	 * @return \Core\Application
	 */
	public function setControllersDir($controllersDir) {
		if(is_dir($controllersDir)) {
			$this->controllersDir = $controllersDir;
		} else {
			throw new \Exception("$controllersDir is not a valid directory");
		}
		return $this;
	}
	/**
	 * Set the views directory
	 * @param string $viewsDir Directory where the views are located
	 * @return \Core\Application
	 */
	public function setViewsDir($viewsDir) {
		if(is_dir($viewsDir)) {
			$this->viewsDir = $viewsDir;
		} else {
			throw new \Exception("$viewsDir is not a valid directory");
		}
		return $this;
	}
	/**
	 * Execute the request (It deserved it)
	 */
	public function run() {
		
		//set controller and view directory
		if ($this->router->isAdmin) {
			$requestedController = $this->getAdminControllersDir() . $this->router->getController() . '.php';
			$requestedView = $this->getAdminViewsDir();
		}else{
			$requestedController = CTRL . $this->router->getController() . '.php';
			$requestedView = VIEWS;
		}

		//check if controller exist and include it
		is_file($requestedController) ?: $this->error404();
		include_once $requestedController;

		//get controller
		$controller = $this->router->getController();

		//instanciate controller
		$controller = new $controller($requestedView);

		//set url_from
		if(strtolower($_SERVER['REQUEST_METHOD']) != 'post'){ $controller->save_navigation(); }
		
		//get method
		$method = $this->router->getMethod();

		//check if controller exist
		if (!method_exists($controller, $method)){ $this->error404(); }

		//run method and set params
		$controller->setParams($this->router->getParams())->$method();
	}
	/**
	 * 404 method
	 * @return void
	 */
	public function error404() {
		echo '404 page not found !!!';
		exit();
	}
}
