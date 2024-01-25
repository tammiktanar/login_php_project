<?php
include('session.php');
class Process {
	
	var $kl;
	
	function __construct() {
		global $session;
		$this->kl = new Db;		
		if(isset($_POST['subjoin'])){			# Registreerimine
			$this->procRegister();
		} else if(isset($_POST['sublogin'])){	# Sisse logimine
			$this->procLogin();
		} else {								# Välja logimine
			$this->procLogout();
		} 		
	} 
	
	# Registreerimine
	function procRegister() {
		global $session;
		
		$fullname =  $this->kl->getVar('user_fullname');
		$username =  $this->kl->getVar('user_name');
		$password = $this->kl->getVar('user_password');
		$password_check = $this->kl->getVar('user_password_2');


		echo "as";
		$_SESSION['post-data']= $_POST; #Kogu vormi info massiivi

		if (strlen($fullname) > 250) {
			$_SESSION['error']['alert'] = "Konto täisnimi on liiga pikk!";
			$_SESSION['error']['success'] = false;
			header("Location: ".$_SERVER["HTTP_REFERER"]); 
		} else if (strlen($fullname) < 3) {
			$_SESSION['error']['alert'] = "Konto täisnimi on liiga lühike!";
			$_SESSION['error']['success'] = false;
			header("Location: ".$_SERVER["HTTP_REFERER"]); 
		}

		if (strlen($username) > 250) {
			$_SESSION['error']['alert'] = "Konto nimi on liiga pikk!";
			$_SESSION['error']['success'] = false;
			header("Location: ".$_SERVER["HTTP_REFERER"]); 
		} else if (strlen($username) < 3) {
			$_SESSION['error']['alert'] = "Konto nimi on liiga lühike!";
			$_SESSION['error']['success'] = false;
			header("Location: ".$_SERVER["HTTP_REFERER"]);	
		}

		if (strlen($password) > 250) {
			$_SESSION['error']['alert'] = "Konto parool on liiga pikk!";
			$_SESSION['error']['success'] = false;
			header("Location: ".$_SERVER["HTTP_REFERER"]); 
		} else if (strlen($password) < 3) {
			$_SESSION['error']['alert'] = "Konto parool on liiga lühike!";
			$_SESSION['error']['success'] = false;
			header("Location: ".$_SERVER["HTTP_REFERER"]); 
		} else if ($password != $password_check) {
			$_SESSION['error']['alert'] = "Konto paroolid ei klappi!";
			$_SESSION['error']['success'] = false;
			header("Location: ".$_SERVER["HTTP_REFERER"]); 
		}

		$result = $session->register($fullname, $username, $password);

		if ($result == 0) {
			$_SESSION['error']['alert'] = "Konto tehtud!";
			$_SESSION['error']['success'] = true;
			header("Location: index");		
			unset($_SESSION['post-data']);	
		} else if ($result == 1) {
			$_SESSION['error']['alert'] = "Konto tegemisel oli viga!";
			$_SESSION['error']['success'] = false;
			header("Location: ".$_SERVER["HTTP_REFERER"]); # Juhul kui on valed logimise andmed			
		} else if ($result == 2) {
			$_SESSION['error']['alert'] = "Konto nimi on juba olemas";
			$_SESSION['error']['success'] = false;
			header("Location: ".$_SERVER["HTTP_REFERER"]); # Juhul kui on valed logimise andmed			
		}
		
	}

	# Sisse logimine
	function procLogin(){
		global $session;
		$username =  $this->kl->getVar('user_name');
		$password = $this->kl->getVar('user_password');

		$result = $session->login($username, $password);
		if($result) {
			$_SESSION['error']['alert'] = "Tere tulemast!";
			$_SESSION['error']['success'] = true;
			header("Location: index");			
		} else {
			$_SESSION['error']['alert'] = "Kasutaja logimise info on vigane!";
			$_SESSION['error']['success'] = false;
			header("Location: ".$_SERVER["HTTP_REFERER"]); # Juhul kui on valed logimise andmed			
		}
	}

	# Välja logimine
	function procLogout(){
		global $session;
		$session->logout();
		header("Location: index");
	}
}
$process = new Process;
?>