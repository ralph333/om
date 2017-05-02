<?php
class OmSsh {
	private  $ssh_conn_passwd;
	public function ssh_connection_passwd() {
		$this->ssh_conn_passwd = ssh2_connect ( Yaf_Application::app ()->getConfig ()->application->ssh->host, Yaf_Application::app ()->getConfig ()->application->ssh->port );
		if (! $this->ssh_conn_passwd) {
			$this->log .= "SSH Connection failed !";
			throw new Exception($this->log);
			return $this->log;
		}
	}
	public function authPassword($user, $password) {
		if (! ssh2_auth_password ( $this->ssh_conn_passwd, $user, $password )) {
			$this->log .= "SSH Authorization failed !";
			throw new Exception($this->log);
			return $this->log;
		}
	}
	public function sshCmd_passwd( $cmd)
	{
		if( !ssh2_exec( $this->ssh_conn_passwd, $cmd) )
		{
			$this->log .= "ssh cmd failed !";
			throw new Exception($this->log);
			return  $this->log;
		}
	}
	public function sshCmd_passwd_result($cmd)
	{
		$result_value = ssh2_exec( $this->ssh_conn_passwd, $cmd);
		if( !$result_value )
		{
			$this->log .= "ssh cmd_result failed !";
			throw new Exception($this->log);
			return  $this->log;
		}
		stream_set_blocking($result_value, true);
		// Return the result and delete the files
		$result = stream_get_contents($result_value);
		return $result;
	}
	public function sshCmd_passwd_noresult($cmd)
	{
	    $result_value = ssh2_exec( $this->ssh_conn_passwd, $cmd);
	    if( !$result_value )
	    {
	        $this->log .= "ssh cmd_result failed !";
	        throw new Exception($this->log);
	        return  $this->log;
	    }
	    
	}
	public function sshShell_passwd_result( $cmd)
	{
		$shell = ssh2_shell( $this->ssh_conn_passwd, 'xterm', null, 120, 24, SSH2_TERM_UNIT_CHARS);
		fwrite( $shell, $cmd.PHP_EOL);
		sleep(1);
		fwrite( $shell, 'end'.PHP_EOL);
		#sleep(1);
		stream_set_blocking($shell, true);
		$sshResult = "";
		while ($buf = fgets($shell,4096)) {
			//        flush();
			$sshResult .= $buf;
			if (strpos($buf, 'end') !== false)
			{
				break;
			}
		}
		fclose($shell);
		//echo $sshResult;
		return $sshResult;
	}
	public function sshCmd_try($cmd)
	{
	    try
	    {
	       $this->ssh_connection_passwd();
	       $this->authPassword(Yaf_Application::app ()->getConfig ()->application->ssh->username, Yaf_Application::app ()->getConfig ()->application->ssh->password);
	       $result = $this->sshCmd_passwd_result($cmd);
	    }
	    catch(Exception $e)
	    {
	        echo $e->getMessage();
	        return false;
	        exit;
	    }
	    return $result;  
	}
	public function sshCmd_try_noresult($cmd)
	{
	    try
	    {
	        $this->ssh_connection_passwd();
	        $this->authPassword(Yaf_Application::app ()->getConfig ()->application->ssh->username, Yaf_Application::app ()->getConfig ()->application->ssh->password);
	        $this->sshCmd_passwd_noresult($cmd);
	    }
	    catch(Exception $e)
	    {
	        echo $e->getMessage();
	        return false;
	        exit;
	    }
	    
	}
	public function sshCmd_java_try($cmd)
	{
	    try
	    {
	        $this->ssh_connection_passwd();
	        $this->authPassword(Yaf_Application::app ()->getConfig ()->application->ssh->java->username, Yaf_Application::app ()->getConfig ()->application->ssh->java->password);
	        $result = $this->sshCmd_passwd_result($cmd);
	    }
	    catch(Exception $e)
	    {
	        echo $e->getMessage();
	        return false;
	        exit;
	    }
	    return $result;
	}
	public function sshShell_java_try($cmd)
	{
	    try
	    {
	        $this->ssh_connection_passwd();
	        $this->authPassword(Yaf_Application::app ()->getConfig ()->application->ssh->java->username, Yaf_Application::app ()->getConfig ()->application->ssh->java->password);
	        $result = $this->sshShell_passwd_result($cmd);
	    }
	    catch(Exception $e)
	    {
	        echo $e->getMessage();
	        return false;
	        exit;
	    }
	    return $result;
	}
	
	
}