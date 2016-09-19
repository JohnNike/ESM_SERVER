<?php if(!defined('INDEX')){exit;}

class dbHandler {
	protected static $SUPPRESS_ERRORS = TRUE;
	protected $DB_HOST = null;
	protected $DB_NAME = null;
	protected $DB_USER = null;
	protected $DB_PASSWORD = null;

	private $DBLINK = NULL;

	public function __construct($DB_NAME, $DB_HOST = 'localhost', $DB_USER = '', $DB_PASSWORD = '') {
		$this->DB_NAME = $DB_NAME;
		$this->DB_HOST = $DB_HOST;
		$this->DB_USER = $DB_USER;
		$this->DB_PASSWORD = $DB_PASSWORD;

		try {
			$this->DBLINK = mysqli_connect($this->DB_HOST, $this->DB_USER, $this->DB_PASSWORD, $this->DB_NAME);
			if (mysqli_connect_errno($this->DBLINK)) $this->dieErr(mysqli_connect_error());
			if (mysqli_query($this->DBLINK, "SET NAMES utf8")) ;
			if ($this->DBLINK->errno) $this->dieErr($this->DBLINK->error);
		} catch (Exception $e) {
			$this->dieErr($e);
		}
	}

	public function killDbLink() {
		if ( $this->DBLINK ) {
			$this->DBLINK->close();
		}
	}

	protected function dieErr($er) {
		self::$SUPPRESS_ERRORS ? die(): die($er);
	}


	public function passEncrypt($password)  {
		return trim(self::base64_url_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, ServerKey, $password, MCRYPT_MODE_CBC, md5("19590704", true))));
	}


	public function passDecrypt($password)  {
		return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, ServerKey, trim(self::base64_url_decode($password)), MCRYPT_MODE_CBC, md5("19590704", true)));

	}

	public static function base64_url_encode($input) {
		return strtr(base64_encode($input), '+/=', '-_,');
	}


	public static function base64_url_decode($input) {
		return base64_decode(strtr($input, '-_,', '+/='));
	}

	public static function userToDBDate($v) {
		if($v <> '') {
			$myDateTime = date_create_from_format('d/m/Y', $v);
			return date_format($myDateTime,'Y-m-d');
		}
		else
			return $v;
	}


	public static function DBToUserDate($v) {
		if ( ($v == '0000-00-00') || ($v == '') ) {
			return '';
		}
		else {
			$myDateTime = date_create_from_format('Y-m-d', $v);
			return date_format($myDateTime,'d/m/Y');
		}
	}



	function cleanupParam($str, $encode_ent = false)
	{
		$haveQuotes = ( function_exists('get_magic_quotes_gpc') && @get_magic_quotes_gpc() == true);
		$str = @trim($str);
		if($encode_ent) {
			$str = htmlspecialchars($str);
		}
		if(version_compare(phpversion(),'4.3.0') >= 0) {
			if( $haveQuotes) {
				$str = @stripslashes($str);
			}
			if(@mysqli_ping($this->DBLINK)) {
				$str = $this->DBLINK->real_escape_string($str);
			}
			else {
				$str = addslashes($str);
			}
		}
		else {
			if(!$haveQuotes) {
				$str = addslashes($str);
			}
		}
		return $str;
	}


	public function fetch($query) {
		$r = $this->DBLINK->query($query) or $this->dieErr($this->DBLINK->error);
		if  ( $r ) {
			if ($r->num_rows > 0) {
				$resp = $r->fetch_assoc();
				$r->free();
				return $resp;
			}
			$r->free();
		}
		return null;
	}

	public function fetchAll($query) {
		$r = $this->DBLINK->query($query) or $this->dieErr($this->DBLINK->error);
		if  ( $r ) {
			if ($r->num_rows > 0) {
				$result = array();
				while ($rec = $r->fetch_assoc()) {
					$result[] = $rec;
				}
				$r->free();
				return $result;
			}
			$r->free();
		}
		return null;
	}


	public function select($query, $multipleResults = false) {
		return !$multipleResults ? $this->fetch($query) : $this->fetchAll($query);
	}

	public function insert($query) {
		$r = $this->DBLINK->query($query) or $this->dieErr($this->DBLINK->error);
		if ($r) return $this->DBLINK->insert_id;
		return false;
	}

	public function update($query) {
		$r = $this->DBLINK->query($query) or $this->dieErr($this->DBLINK->error);
		if ($r) return $this->DBLINK->affected_rows;
		return false;
	}

}
