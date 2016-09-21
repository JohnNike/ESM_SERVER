<?php if(!defined('INDEX')){exit;}


class userModel extends JnPHP {
	public $userId;
	public $userName;
	public $userEmail;
	public $userPass;

	protected $db;

	public function __construct($db) {
		$this->db = $db;
	}

	public function fetchObj($userId) {
		$query = "SELECT * FROM user WHERE userId=$userId";
		$nUser = $this->getDb()->fetch($query);
		if ($nUser) {
			$this->userId = $nUser['userId'];
			$this->userName = $nUser['userName'];
			$this->userEmail = $nUser['userEmail'];
			$this->userPass = $nUser['userPass'];
		}
		return $this;
	}

	public function fetch($userId) {
		$query = "SELECT * FROM user WHERE userId=$userId";
		$nUser = $this->db->fetch($query);
		if ($nUser) {
			$this->userId = $nUser['userId'];
			$this->userName = $nUser['userName'];
			$this->userEmail = $nUser['userEmail'];
			$this->userPass = $nUser['userPass'];
		}
		return $nUser;
	}

	public function insert() {
		$query = "INSERT INTO user VALUES (NULL, '$this->userName', '$this->userEmail', '$this->userPass')";
		return $this->getDb()->fetch($query);
	}

	public function update() {
		$query = "UPDATE user SET userName='$this->userName', userEmail='$this->userEmail',userPass='$this->userPass' WHERE id=$this->userId";
		return $this->getDb()->fetch($query);
	}


}
