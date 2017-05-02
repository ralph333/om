<?php
class LoginController extends Yaf_Controller_Abstract {
	public function init() {
		$this->getView ()->setLayout ( 'PageHead' );
	}
	public function indexAction()
	{
		
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