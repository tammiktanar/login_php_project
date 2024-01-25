<?php
	# Konstandid andmebaasiga ühendamiseks
	define("DB_SERVER", "localhost");
	define("DB_USER", "root");
	define("DB_PASS", "");
	define("DB_NAME", "project");
	

class Db {

	function getScriptKaust() {
		return "/login_php_project/";
	}

	# Andmebaasi ühendus
	function dbConnect() {
		$con = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);			
		if ($con->connect_errno) {
			echo "<strong>Viga andmebaasiga:</strong> ".$con->connect_error;
			return false;
		}
		mysqli_set_charset($con, "utf8");
		return $con;
	}
	
	# Andmebaasist küsimine ja tulemuse saamine. SELECT result massiivina 
	# Käsklus mille kutsud välja oma skriptis
	function dbGetArray($sql) {
		$res = $this->dbQuery($sql);		
		if ($res !== false) {
			$data = array();			
			if($res) {
				while($row = mysqli_fetch_assoc($res)) {
					$data[] = $row;
				}
				if (is_array($data) and count($data) > 0) {
					return $data;
				} else {
					return false;
				}
			} else {
				echo '<div class="message punane"><strong>Viga andmebaasist k&uuml;simisega</strong></div>';
				return false;
			}
		} else {
			return false;
		}
	}
	
	# UPDATE, INSERT ja DELETE käskluste tegemiseks ja SELECT tulemuse saamiseks. 
	# Käsklus mille kutsud välja oma skriptis
	function dbQuery($sql) {
		$con = $this->dbConnect();
		if($con) {
			$res = mysqli_query($con, $sql);		
			if ($res === false) {			
				echo '<div class="message punane"><strong>Vigane SQL p&auml;ring:</strong> '.$sql.'</div>';
				return false;
			}
			mysqli_close($con);
			return $res;
		}
		return false;
	}
	

	# Andmebaasi lisamiseks/muutmiseks väljade varjestamiseks. Teksti muutujad saavad "" ümber. 
	# Numbrid mitte. 
	# Käsklus mille kutsud välja oma skriptis
	function dbFix($var) {
		if (!is_numeric($var)) {
			$var = '"'.addSlashes($var).'"';
		}
		return $var;
	}
	
	# Vormilt saadud info kontrolimiseks
	# Käsklus mille kutsud välja oma skriptis
	function getVar($name)  {
		# Kas on olemas GET ja kas see on masiiv
		$var = false;
		if (isset($_GET) and is_array($_GET)) {
			if (isset($_GET[$name])) {
				$var = $_GET[$name];
			}
		} else {
			global $HTTP_GET_VARS;
			if (isset($HTTP_GET_VARS[$name])) {
				$var =  $HTTP_GET_VARS[$name];
			}
		}
		# Kas on olemas POST ja kas see on masiiv
		if (isset($_POST) and is_array($_POST)) {
			if (isset($_POST[$name])) {
				$var =  $_POST[$name];
			}
		} else {
			global $HTTP_POST_VARS;
			if (isset($HTTP_POST_VARS[$name])) {
				$var =  $HTTP_POST_VARS[$name];
			}
		}

		return $var;
	} # getVar lõpp
	
	# Näitab PHP massiivi (Array) inimlikul kujul 
	# Käsklus mille kutsud välja oma skriptis
	function naita($array) {
		echo '<pre>'; // Eelvormindatud tekst
		print_r($array);
		echo '</pre>';
	}	
	
	# Muudab andmebaasi kuupäeva kujult YYYY-MM-DD
	# kujule DD.MM.YYYY
	function dbDateToEstDate($date) {
		return date('d.m.Y', strtotime($date));
	}
	
	# Muudab andmebaasi kuupäeva kujult YYYY-MM-DD HH:MM:SS
	# kujule DD.MM.YYYY HH:MM:SS
	function dbDateToEstDateClock($date) {
		return date('d.m.Y H:i:s', strtotime($date));
	}
		
	# Andmebaasi kuupäevast eemaldatakse kella osa. Kuju jääb ikka andmebaasiks
	# Muudab andmebaasi kuupäeva kujult YYYY-MM-DD HH:MM:SS => YYYY-MM-DD
	function dbDateRemoveClock($date) {
		return date('Y-m-d', strtotime($date));
	}
	
	# Kuupäev kujul MM-DD muudetakse kujule DD. KUUNIMI (22. Jaanuar)
	function dbMDtoDMM($date) {
		$kuunimed = array('','Jaanuar','Veebruar','Märts','Aprill','Mai','Juuni','Juuli','August','September','Oktoober','November','Detsember');
		$osad = explode('-', $date);
		$result = $osad[1].'. '.$kuunimed[(int)$osad[0]];
		return $result;
	}
	
	
	
}
# Ilma selle reata on eelnevad klassis olevad funktsioonid kasutud
# Muutuja ($kl) mida kasutad enda skriptides antud klassis olevate funktsioonide välja kutsumiseks
# Näiteks: $kl->naita($massiiv); # Näitab muutja massiiv ($massiiv) sisu inimlikul kujul.
$kl = new Db;
?>