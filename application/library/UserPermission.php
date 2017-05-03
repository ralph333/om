<?php
class UserPermission extends Yaf_Plugin_Abstract
{
	public function preDispatch(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) 
	{
		session_start();
		$authList = array('index' => array('index'),
		                  'login' => array('index'),
		                  'user'  => array('auth'),
		                  'monitor' => array('server'), 
		                  'release' => array('dns_api','release_api'),
		                
		                  );
		$controllerName =  strtolower($request->getControllerName());
		$actionName = strtolower($request->getActionName());
		if(in_array($controllerName, array_keys($authList)))
		{
			if(in_array($actionName, $authList[$controllerName]))
			{
				return true;
			}
		}
		if ($_SESSION['username']=="" || !isset($_SESSION['username'])) {
			$refer = urlencode($_SERVER['REQUEST_URI']);
			$url = '';
			if(!empty($refer))
			{
				$url = '?refer='.$refer;
			}
			header("Location: /login".$url);
			exit;
		}
		else 
		{
			if($controllerName == 'user' && $actionName == 'password')
			{
			  return true;	
			}
			else
			{
				$db_conn = new OmMysql ();
				$row = $db_conn->mysqli_query("login_sql.password_check", array('username'=> $_SESSION['username']));
				
				$result = mysql_fetch_array($row);
				print_r($result);
				if($result['confirm_password'] == 0)
				{
					header("Location: /user/password");
				}
			}
		}
	}
}


