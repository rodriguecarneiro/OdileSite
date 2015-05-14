<?php

namespace Core;

class Controller
{
	/**
	 * @param bool $isCacheable Weither or not we should cache the response
	 */
	public $isCacheable = false;
	/**
	 * @param int $maxAge The max-age delta to use for the Cache-Control directive
	 */
	public $maxAge = 300;
	/**
	 * @param String $cacheability The cacheability of the response (public, private or no-cache)
	 */
	public $cacheability;

	private $tplDir;
	protected $_params = array();
	/**
	 * @param \Core\Response The HTTP Response Object
	 */
	public $response;
	/**
	 * Constructor
	 * If overloaded, make sure to forward the $tplDir
	 * @param String $tplDir The template directory
	 */

    public function __construct($tplDir) {

		//admin security
		$this->isAllowed();

		//set template dir
		$this->tplDir = $tplDir;

		//set helper property
		$this->helper = new Helper();

    	// set app tools 
    	if(isset($_SESSION['user']['user_id'])){
    		
	    	$app = new App();

	    	$this->app = $app;
	    	$this->me = $this->getModel('user')->getOne(['id' => $_SESSION['user']['user_id']]);
			$this->isAdmin = $_SESSION['user']['admin'] ? true : false;

	    	//notifications
	    	//$this->userNotifications = $app->getUserNotifications();
	    	//$this->UnreadNotifications = $app->getUnreadNotifications();

	    	//messages
	    	// $this->userMessages = $app->getUserMessages();
	    	// $this->UnreadMessages = $app->getUnreadMessages();
    	}

		// Model auto-instanciation in controller
		$className = str_replace("Controller", "", get_class($this));
		$this->type = $className;
		$this->model = $this->getModel($className);

		$this->response = new Response($this->isCacheable);
		if($this->isCacheable) {
			$this->response->setMaxAge($this->maxAge);
			if(!is_null($this->cacheability)) {
				$this->response->setCacheability($this->cacheability);
			}
		}
	}
	/**
	* Set the URI Submitted parameters for Controller/template access
	* @param array
	* @return \Core\Controller
	*/
	public function setParams(array $params) {

		//set param id for show view
		if (isset($params[0]) && is_numeric($params[0])) {
			$this->_params['id'] = (int) $params[0];
		}

		// set various param
		if (isset($params[1]) && sizeof($params[1]) > 0) {
			foreach ($params[1] as $various) {
				$this->_params[$various] = '';
			}
		}

		return $this;
	}
	/**
	 * Return User submitted data (in URI)
	 * @param String $entry The name of the data
	 * @param mixed $default A default value for the data, used if data entry is non existent
	 * @return mixed
	 */
	public function getParam($entry, $default = null) {
		if(isset($this->_params[$entry])) {
			return $this->_params[$entry];
		}
		return $default;
	}
	/**
	 * Return GET content variable if set
	 * @param String The key name
	 * @param mixed $default A default value to use when entry is non existent
	 * @return mixed
	 */
	public function getGET($entry, $default = null) {
		if(isset($_GET[$entry])) {
			return $_GET[$entry];
		}
		return $default;
	}
	/**
		* Return POST content variable if set
	 * @param String The key name
	 * @param mixed $default A default value to use when entry is non existent
	 * @return mixed
	 */
	public function getPOST($entry, $default = null) {
		if(!empty($_POST[$entry])) {
			if (!is_array($_POST[$entry]))
				return trim($_POST[$entry]);
			else
				return $_POST[$entry];
		}
		return $default;
	}
	/**
	 * Return COOKIE content variable if set
	 * @param String The key name
	 * @param mixed $default A default value to use when entry is non existent
	 * @return mixed
	 */
	public function getCOOKIE($entry, $default = null) {
		if(isset($_COOKIE[$entry])) {
			return $_COOKIE[$entry];
		}
		return $default;
	}
	/**
	 * Simple wrapper for redirection
	 * @param String $url The url to redirect to
	 * @param int $code The HTTP Code to use. Default: 301
	 */
	public function redirect($url, $code = 301) {
		header("Location: $url", true, $code);
		exit;
	}
	/**
	 * Renders the template and append the content to the HTTP Response
	 * @param String $tpl Path to the template. It is better to add the view folder to include paths
	 * @return void
	 */
	public function render($sView = "index", $vars = array(), $options = array(), $tpl = 'layout') {

		//options de render
		$this->view = $this->type . '/' . $sView . '.html';

		//save options
		foreach ($options as $option => $values) {
			$this->options[$option] = array($values);
		}

		//include variables in view
		extract($vars);

		ob_start();

		//get layout content
		if(is_file($this->tplDir . $tpl . '.html')){
			include $this->tplDir . $tpl . '.html';
		}

		$content = ob_get_contents();
		ob_end_clean();

		$this->response->append($content);
		$this->sendResponse();
	}
	/**
	 * Proxy function to \Core\Response->send()
	 * @param int $code The Response HTTP Code
	 * @return void
	 */
	public function sendResponse($code = 200) {
		$this->response->send($code);
	}
	/**
	 * Weither or not the incoming request was made using HTTPS
	 * @return bool
	 */
	public function isHTTPS() {
		if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
        || $_SERVER['SERVER_PORT'] == 443) {
			return true;
		}
		return false;
	}
	/**
	 * Model loader
	 * @param string
	 * @return object
	 */
	public function getModel($model) {
		$class = '\Models\\' .ucfirst($model);
		return new $class;
	}

	public function setUris() {

		$lenght = 5;
		$url = explode('/', $_SERVER["REQUEST_URI"]);

		if(!in_array('assets', $url) and !in_array('api', $url)) {
			if(!isset($_SESSION['nav'])){
				$_SESSION['nav'][] = $_SERVER['REQUEST_URI'];
			}else{
				if($_SESSION['nav'][0] !== $_SERVER['REQUEST_URI']){

					$array = array_reverse($_SESSION['nav']);
					$array[] = $_SERVER['REQUEST_URI'];
					$array = array_slice($array, -$lenght, $lenght);
					$_SESSION['nav'] = array_reverse($array);
				}
			}
		}
	}

	public function getRequestedUri($offset = 'last')
	{
		if($offset === 'last'){
			return $_SESSION['nav'][1];
		}else{
			return $_SESSION['nav'][$offset];
		}
	}

	public function isAllowed()
	{
		$router = new Router();
		if ($router->getRequest() !== '/admin/auth' && $router->getRequest() !== '/admin/auth/login') {

			$request = explode('/', $_SERVER['REQUEST_URI']);
			$uri = array_splice($request, 1);
			if ($uri[0] == "admin" && !isset($_SESSION['user'])) {
				$this->redirect('/admin/auth');
			};
		}
	}

	public function notFound()
	{
		$app = new Application();
		$app->error404();
	}
}

