<?php
include('password.php');
class User extends Password{

    private $_db;

    function __construct($db){
    	parent::__construct();

    	$this->_db = $db;
    }

	private function get_user_hash($username){
	/*agrego BINARY al Where en le select y columna permiso; agrego AND resetComplete="Yes"*/
		try {
			$stmt = $this->_db->prepare('SELECT password, username, memberID, permiso FROM members WHERE BINARY username = :username AND active="Yes" AND resetComplete="Yes"');
			$stmt->execute(array('username' => $username));
			return $stmt->fetch();

		} catch(PDOException $e) {
		    //echo "<br><br><div class='alert alert-danger' >".$e->getMessage().'</div>';
			//muestra error
			require('layout/headerror.php'); 	
			echo "(user) ".$e->getMessage()."</div></div></div>";
			
		}
	}

	public function login($username,$password){
		$row = $this->get_user_hash($username);

		if($this->password_verify($password,$row['password']) == 1){

		    $_SESSION['loggedin'] = true;
		    $_SESSION['username'] = $row['username'];
		    $_SESSION['memberID'] = $row['memberID'];
			
			/*agrega mrmarin, 2017*/
					$_SESSION['permiso'] = $row['permiso'];
                    $_SESSION['ultima_actividad'] = time(); //hora inicio sesion
                    $_SESSION['tiempo_expira'] = 0.5*60*60; //media hora sin actividad, logout automatico
			
			
		    return true;
		}
	}

	public function logout(){
		session_destroy();
	}

	public function is_logged_in(){
		if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true){
			return true;
		}
	}

}


?>
