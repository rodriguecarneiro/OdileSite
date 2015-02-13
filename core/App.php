<?php

namespace Core;
use PDO;

class App extends \Core\Crud
{

	public function __construct(){
		parent::__construct();
	}

	public function getUserNotifications(){

		$userLoggedIn = $_SESSION['user']['user_id'];

		$sql = "SELECT * FROM `notifications` WHERE `user_id_to` = $userLoggedIn ORDER BY `notification_id` DESC";
		$query = $this->oConnect->query($sql);

		while ($fetch = $query->fetch(PDO::FETCH_OBJ)) {
			$results[] = $fetch;
		}

		return !empty($results)?$results:array();
	}

	public function getUnreadNotifications(){

		$userLoggedIn = $_SESSION['user']['user_id'];

		$sql = "SELECT count(*) as nb_notifs FROM `notifications` WHERE `status` = 'unread' AND `user_id_to` = $userLoggedIn";
		$query = $this->oConnect->query($sql);

		$fetch = $query->fetch(PDO::FETCH_OBJ);

		return !empty($fetch->nb_notifs)?$fetch->nb_notifs:0;
	}

}