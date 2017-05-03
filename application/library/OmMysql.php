<?php
class OmMysql
{
	private  $conn;
	public  function __construct()
	{
		$this->conn = mysqli_connect(Yaf_Application::app()->getConfig()->application->mysql->host,
									Yaf_Application::app()->getConfig()->application->mysql->username,
									Yaf_Application::app()->getConfig()->application->mysql->password,
		                            Yaf_Application::app()->getConfig()->application->mysql->database
									);
		if (!$this->conn) {
			die(mysql_error());
		}
	}

	public function mysql_query($router, $params)
	{
		$arr = explode('.', $router);
		$project = $arr[0];
		$section = $arr[1];
		$result = parse_ini_file(Yaf_Application::app()->getConfig()->application->directory.'/sqlmap/'.$project.'/'.$project.'.ini', true);
		$sql = $result[$section];
		$patterns = array();
		foreach($params as $key => $val)
		{
			$patterns[$key] = sprintf("/:%s/", $key);
		}
		$sql = preg_replace($patterns, $params, $sql);
		//echo $sql;
		return mysqli_query($this->conn, $sql);
	}

	public function mysql_num_rows($row)
	{
		return mysql_num_rows($row);
	}
}