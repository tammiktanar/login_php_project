<?php
include('mysqli.php');	// Andmebaasi ühendused

class Session {
	var $fullname;				# Täis nimi
	var $username;     			# Kasutajanimi
	var $userid;       			# Juhuslik kasutaja id
	var $userlevel;    			# Kasutaja level (tase)
	var $time;         			# Millal oli viimati kasutaja aktiivne (laeti lehte)
	var $loggedIn;    			# True kui kasutaja on sisseloginud, false kui pole
	var $userinfo = array();  	# Massiiv hoiab kasutaja infot
	var $url;          			# Hetkel vaatamisel olev lehekülg
	var $referrer;     			# Viimane lehekülg
	var $kl;					# Andmebaasi värgid
	var $USER_TIMEOUT = 1800;	# 1800 = 30 minutit
		
	# Klassi konstruktor
	function __construct(){			
		$this->kl = new Db;		# Andmebaasi ühendus jaoks
		$this->time = time();		
		$this->startSession();		
	}
	
	# Sessiooni käivitamine
	function startSession() {
		session_start();
		$this->loggedIn = $this->checkLogin();	// Kas on sisseloginud või mitte
		if(!$this->loggedIn) { 
			# Ei ole sisse loginud
			$this->username = $_SESSION['username'] = "Kasutaja";
			$this->userlevel = 0;			
			$sql = 'REPLACE INTO active_guests VALUES ('.$this->kl->dbFix($_SERVER["REMOTE_ADDR"]).','.$this->kl->dbFix(date("Y-m-d H:i:s", $this->time)).')';
			$this->kl->dbQuery($sql);
		} else {
			# On sisse loginud
			# UUENDA KASUTAJA aktiivsust
			$sql = 'REPLACE INTO active_users VALUES ('.$this->kl->dbFix($this->username).', '.$this->kl->dbFix(date("Y-m-d H:i:s", $this->time)).')';
			$this->kl->dbQuery($sql);
			$lastTime = strtotime($_SESSION['lastActivity']);
			
			if(($this->time - $lastTime) >= $this->USER_TIMEOUT) {
				$this->logout();
			} else {
				$_SESSION['lastActivity'] = date("Y-m-d H:i:s", $this->time);
			}
		}
		# Set referrer page (Ei toimi alati nii nagu vaja)
		if(isset($_SESSION['url'])){
			$this->referrer = $_SESSION['url'];
		} else {
			$this->referrer = "/";
		}

		# Set current url (Ei toimi alati nii nagu vaja)
		$this->url = $_SESSION['url'] = $_SERVER['PHP_SELF'];
	}
	
	# Kontrollime kas on sisse loginud
	function checkLogin() {
		if(isset($_SESSION['username']) && isset($_SESSION['userid'])) {
			$retBool = $this->checkLoggedUser($_SESSION['username'], $_SESSION['userid']);
			if(!$retBool) {
				unset($_SESSION['fullname']);
				unset($_SESSION['username']);
				unset($_SESSION['userid']);
				unset($_SESSION['userlevel']);
				unset($_SESSION['loggedin']);
				unset($_SESSION['logged']);
				unset($_SESSION['lastActivity']);
				return false;
			}
			$sql = 'SELECT * FROM users WHERE username = "'.$_SESSION['username'].'"';
			$res = $this->kl->dbGetArray($sql);
			$this->fullname	 = $res[0]['fullname'];
			$this->username  = $res[0]['username'];
			$this->userid    = $res[0]['userid'];
			$this->userlevel = $res[0]['userlevel'];			
			$this->loggedIn = true;			
			return true;
		}
	}
	
	# Kas on sisseloginud kasutaja
	function checkLoggedUser($username, $userid) {
		$sql = 'SELECT * FROM users WHERE username = "'.$username.'" and userid = "'.$userid.'"';
		$res = $this->kl->dbGetArray($sql);
		if($res !== false) {
			return true;
		} else {
			return false;
		}
	}
	
	# Sisse logimine
	function login($user, $pass) {
		
		$sql = 'SELECT * FROM users WHERE username = "'.$user.'" and password = "'.md5($pass).'"';
		$res = $this->kl->dbGetArray($sql);

		if($res !== false) {
			//$this->kl->naita($res);
			$this->fullname	 = $_SESSION['fullname'] = $res[0]['fullname'];
			$this->username  = $_SESSION['username'] = $res[0]['username'];
			$this->userid    = $_SESSION['userid']   = $this->generateRandID();
			$this->userlevel = $_SESSION['userlevel'] = $res[0]['userlevel'];
			$this->loggedIn  = $_SESSION['loggedin'] = true;
			$_SESSION['logged']	 		 = date("Y-m-d H:i:s",$this->time);
			$_SESSION['lastActivity']	 = date("Y-m-d H:i:s",$this->time);
			$sql = 'UPDATE users SET userid = '.$this->kl->dbFix($this->userid).' WHERE username = '.$this->kl->dbFix($this->username);
			$this->kl->dbQuery($sql);
			$sql = 'REPLACE INTO active_users VALUES ('.$this->kl->dbFix($this->username).', '.$this->kl->dbFix(date("Y-m-d H:i:s", $this->time)).')';
			$this->kl->dbQuery($sql);
			return true;
		}
		return false;
	}
	
	# Juhuslik string välja kutsumine
	function generateRandID(){
		return md5($this->generateRandStr(16));
	}

	# Juhuslik string
	function generateRandStr($length){
		$randstr = "";
		for($i = 0; $i < $length; $i++){
			$randnum = mt_rand(0,61);
			if($randnum < 10){
				$randstr .= chr($randnum+48);
			}else if($randnum < 36){
				$randstr .= chr($randnum+55);
			}else{
				$randstr .= chr($randnum+61);
			}
		}
		return $randstr;
	}
	
	# Välja logimine
	function logout(){
		/* Unset PHP session variables */
		unset($_SESSION['username']);
		unset($_SESSION['userid']);

		/* Reflect fact that user has logged out */
		$this->loggedIn = false;
		$sql = 'DELETE FROM active_users WHERE username = '.$this->kl->dbFix($this->username);
		$this->kl->dbQuery($sql);
		$sql = 'REPLACE INTO active_guests VALUES ('.$this->kl->dbFix($_SERVER["REMOTE_ADDR"]).', NOW())';
		$this->kl->dbQuery($sql);		
		session_destroy();
	}
	
	# Kas on sisse logitud
	function isLogged() {
		return $this->loggedIn;
	}



	# Registeerimine
	function register( $fullname, $username, $password ){

		$sql = 'SELECT * FROM users WHERE username = '.$this->kl->dbFix($username);
		echo $sql;
		$res = $this->kl->dbGetArray($sql);
		if($res !== false){
			return 2; # Kasutajanimi on kasutusel
		}

		$sql = 'INSERT INTO `users`(`fullname`, `username`, `password`, `userid`, `userlevel`, `blocked`, `time`) VALUES 
		('
			.$this->kl->dbFix($fullname).','
			.$this->kl->dbFix($username).','
			.'"'.md5($password).'",'
			.'"'.'0'.'",'
			.'"'.'1'.'",'
			.'"'.'0'.'",'
			.$this->kl->dbFix(date("Y-m-d H:i:s", $this->time)).'
		)';

		echo $sql;
		
		if ($this->kl->dbQuery($sql)) {
			return 0;
		} else {
			return 1;
		}
	}
}

$session = new Session;

?>