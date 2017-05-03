<?php
class UserController extends Yaf_Controller_Abstract {
	public function getdevuserAction() {
		$db_conn = new OmMysql ();
		
		$row = $db_conn->mysql_query("user_sql.get_dev_user", array());
		header("content-type:application/json");
		$result_array = array();
		while($result = mysql_fetch_row($row))
		{
			$result_array[] = $result;
		}
		echo json_encode($result_array);
		return false;
	}
	
	public function getqauserAction() {
	    $db_conn = new OmMysql ();
	
	    $row = $db_conn->mysql_query("user_sql.get_qa_user", array());
	    header("content-type:application/json");
	    $result_array = array();
	    while($result = mysql_fetch_row($row))
	    {
	        $result_array[] = $result;
	    }
	    echo json_encode($result_array);
	    return false;
	}
	
	public function getpmuserAction() {
	    $db_conn = new OmMysql ();
	
	    $row = $db_conn->mysql_query("user_sql.get_pm_user", array());
	    header("content-type:application/json");
	    $result_array = array();
	    while($result = mysql_fetch_row($row))
	    {
	        $result_array[] = $result;
	    }
	    echo json_encode($result_array);
	    return false;
	}
	
	public function getomuserAction() {
	    $db_conn = new OmMysql ();
	
	    $row = $db_conn->mysql_query("user_sql.get_om_user", array());
	    header("content-type:application/json");
	    $result_array = array();
	    while($result = mysql_fetch_row($row))
	    {
	        $result_array[] = $result;
	    }
	    echo json_encode($result_array);
	    return false;
	}
	
	/*
	 * 用户登录，验证用户名、密码
	 */
	public function authAction()
	{
	    $db_conn = new OmMysql ();
	    $row = $db_conn->mysql_query("user_sql.login_check", array('username'=> $_POST['username'], 'password'=>md5($_POST['password'])));
	
	    header("content-type:application/json");
	    $result = mysql_num_rows($row);
	    if($result=="1")
	    {
	        //session_start();
	        $_SESSION['username']=$_POST['username'];
	    }
	    echo (json_encode($result));
	    return false;
	}
	
	public function passwordAction() {
		
		if(!empty($_REQUEST))
		{
			if($_REQUEST['passwd1'] !== $_REQUEST['passwd2'] || empty($_REQUEST['passwd1']))
			{
				echo '<script type="text/javascript">window.onload=function(){alert("两次密码输入不匹配");window.top.location.href="/user/password";}</script>';
				exit;
			}
			$db_conn = new OmMysql ();
			$row = $db_conn->mysql_query("login_sql.modify_password", array('username'=> $_SESSION['username'],'password'=>md5($_REQUEST['passwd1'])));
			if(!$row)
			{
				die('Error: ' . mysql_error());
			}
			unset($_SESSION);
			session_destroy();
			echo '<script type="text/javascript">window.onload=function(){alert("修改成功");window.top.location.href="/login";}</script>';
		}
		$this->getView ()->setLayout ( 'PageHead' );
	}
	
}
?>