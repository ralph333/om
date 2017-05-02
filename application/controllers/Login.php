<?php
class LoginController extends Yaf_Controller_Abstract {
	public function init() {
		$this->getView ()->setLayout ( 'PageHead' );
	}
	public function indexAction()
	{
		
	}
	public function authAction()
	{
	    $db_conn = new OmMysql ();
	    $row = $db_conn->mysql_query("login_sql.login_check", array('username'=> $_POST['username'], 'password'=>md5($_POST['password'])));
	
	    header("content-type:application/json");
	    $result = mysql_num_rows($row);
	    if($result=="1")
	    {
	        session_start();
	        $_SESSION['username']=$_POST['username'];
	    }
	    echo (json_encode($result));
	    return false;
	}
	
// 	public function logoutAction()
// 	{
// 		session_start ();
// 		session_destroy();
// 		$_SESSION = array();
// 		//if (isset($_SESSION['uid']) || isset($_SESSION['username']) || isset($_SESSION['usergroup'])) {
// 			//unset($_SESSION['uid']);
// 			unset($_SESSION['username']);
// 			//unset($_SESSION['usergroup']);
// 		//}
		
// 		header("location: /login");
// 		return false;
// 	}
}
?>