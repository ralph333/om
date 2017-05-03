<?php
class ReleaseController extends Yaf_Controller_Abstract {
    /*
     * 代码发布，定义私有变量
     */
    private $BASE_PATH;
    private $CODE_BASE_PATH;
    private $BACKUP_BASE_PATH;
    private $PACKAGE_BASE_PATH;
    private $DEPLOY_BASE_PATH;
    
    private $PROJECT_CODE_PATH;
    private $PROJECT_BACKUP_PATH;
	private $PROJECT_PACKAGE_PATH;
	private $PROJECT_GIT_URL;
	private $PROJECT_ECS_LIST; 
   
    
	public function init()
	{
	    
	    $allowMethods = array('dns_api', 'release_api');
	    $db_conn = new OmMysql ();
	    $row = $db_conn->mysql_query ( "user_sql.get_user_group", array("username"=>$_SESSION['username']));
	    $group = mysqli_fetch_row($row);

	    if($group['0'] !== 'om' && !in_array($this->_request->getActionName(), $allowMethods))
	    {
	        echo '<script type="text/javascript">window.onload=function(){alert("无权访问");window.top.location.href="http://om.sanqimei.com";}</script>';
	        exit;
	    }
		$this->getView()->setLayout('Consolemain');
	}
	
	public function releaseconfigAction()
	{
	    $action = $_REQUEST ['action'];
	    /*
	     * 初始化连接数据库/application/library/OmMysql.php
	     */
	    $db_conn = new OmMysql ();
	    /*
	     * 项目信息查看
	     */
	    $row = $db_conn->mysql_query ( "release_sql.release_config_browse", array());
	    $result_array = array ();
	    $html = '';
	    while ( $result = mysqli_fetch_array ( $row ) ) {
	        if($result ['project_region'] == 'aliyun')
	        {
	            $region = '阿里云';
	        }
	        if($result ['project_region'] == 'aws')
	        {
	            $region = '亚马逊AWS';
	        }
	        if($result ['project_region'] == 'qcloud')
	        {
	            $region = '腾讯云';
	        };
	        if($result ['project_region'] == 'huidu')
	        {
	            $region = '阿里云-灰度环境';
	        }
	        if($result ['project_region'] == 'test')
	        {
	            $region = '测试环境';
	        }
	        $html .= '<tr>';
	        $html .= '<td class="om_table_td">' . $result ['project_name'] . '</td>';
	        $html .= '<td class="om_table_td">' . $region . '</td>';
	        $html .= '<td class="om_table_td">' . $result ['project_language'] . '</td>';
	        $html .= '<td class="om_table_td">' . $result ['project_repository'] . ':' . $result ['project_repository_url'] . '</td>';
	        $html .= '<td class="om_table_td">' . $result ['project_code_path'] . '</td>';
	        $html .= '<td class="om_table_td">' . $result ['project_ip'] . '</td>';
	    }
	    $this->getView ()->assign ( "html", $html );
	    /*
	     * 项目信息写入
	     */
	    
	    if($action == 'submit') {
	        /*
	         * 判断项目是否存在
	         */
	        $row = $db_conn->mysql_query ( "release_sql.release_config_search_project", array("project_name"=>$_REQUEST['rel_conf_project'], "project_region"=>$_REQUEST['rel_conf_project_region']));
	        if(!$row)
	        {
	            die('Error: ' . mysql_error());
	        }
	        if (mysql_num_rows($row)==1)
	        {
	            echo '<script type="text/javascript">window.onload=function(){alert("项目已存在，无需添加");window.top.location.href="/release/releaseconfig";}</script>';
	            exit;
	        }
	        
	        if (empty($_REQUEST['rel_conf_project']) || 
	            empty($_REQUEST['rel_conf_project_language']) || 
	            empty($_REQUEST['rel_conf_project_repository']) || 
	            empty($_REQUEST['rel_conf_project_repository_url']) || 
	            empty($_REQUEST['rel_conf_project_code_path']) ||
	            empty($_REQUEST['rel_conf_project_ip']))
	        {
	            echo '<script type="text/javascript">window.onload=function(){alert("所有选项必填。");window.top.location.href="/release/releaseconfig";}</script>';
	            exit;
	                    
	        }
	        
	        
	        
	        $ip = preg_replace("{\r\n}","<br>",$_REQUEST['rel_conf_project_ip']);
	        
	        $nginx_ip = preg_replace("{\r\n}","<br>",$_REQUEST['rel_conf_project_nginx_ip']);
	      
	        $row = $db_conn->mysql_query ( "release_sql.release_config_add_new_project", 
	                                       array("project_name" => $_REQUEST['rel_conf_project'],
	                                             "project_language" => $_REQUEST['rel_conf_project_language'],
	                                             "project_repository" => $_REQUEST['rel_conf_project_repository'],
	                                             "repository_url" => $_REQUEST['rel_conf_project_repository_url'],
	                                             "project_code_path" => $_REQUEST['rel_conf_project_code_path'],
	                                             "project_ip" => $ip,
	                                             "project_nginx_ip" => $nginx_ip,
	                                             "project_nginx_conf" => $_REQUEST['rel_conf_project_nginx_conf_path'],
	                                             "project_region" => $_REQUEST['rel_conf_project_region'],
	                                             "project_rookeeper" => $_REQUEST['rel_conf_project_zookeeper']
	                                             ));
	        if(!$row)
	        {
	            die('Error: ' . mysql_error());
	        }
	        
	        
	        echo '<script type="text/javascript">window.onload=function(){alert("添加成功");window.top.location.href="/release/releaseconfig";}</script>';
	        exit;
	    }
	    
	    if($action == 'update') 
	    {
	        $array = explode(',', $_REQUEST['rel_app']);
	        $project_name = $array[0];
	        $project_region = $array[1];
	        $ip = preg_replace("{\r\n}","<br>",$_REQUEST['rel_conf_project_ip']);
	        
	        $row = $db_conn->mysql_query ( "release_sql.release_update_ip", array("project_ip"=>$ip, "project_name"=>$project_name, "project_region"=>$project_region));
	        if(!$row)
	        {
	            die('Error: ' . mysql_error());
	        }
	        echo '<script type="text/javascript">window.onload=function(){alert("更新成功");window.top.location.href="/release/releaseconfig";}</script>';
	    }
	    
	}
	
	public function releasecodeAction()
	{
		/*
		 * 初始化连接数据库/application/library/OmMysql.php
		*/
		$db_conn = new OmMysql ();
		/*
		 * 加载页面分页
		*/
		$page = $_REQUEST['page'];
		if(empty($page)){
			$page = 1;
		}
		$size=10;
		$data_start=($page-1)*$size;
		$data_limit=$size;
		$count_row = $db_conn->mysql_query("release_sql.release_history_browse", array());
		$count = mysql_num_rows($count_row);
		$paging = new Paging ($count,$size,$page);
		$pageViewString = $paging->PageHtml();
		
		$this->getView ()->assign ( "paging", $pageViewString );
		//$this->getView()->assign("content");
		/*
		 * 发布浏览页面
		*/
		$row = $db_conn->mysql_query ( "release_sql.release_history_browse", array("data_start"=>$data_start,"data_limit"=>$data_limit));
		$result_array = array ();
		$html = '';
		while ( $result = mysql_fetch_array ( $row ) ) {
			$result_array [] = $result;
			if($result ['rel_version'] == ""){
				$app = $result ['rel_app'];
			}else {
				$app = $result ['rel_app'] . ":" . $result ['rel_version'];
			}
			$html .= '<tr>';
			$html .= '<td class="rel_browse_table">' . $app . '</td>';
			$html .= '<td class="rel_browse_table">' . $result ['rel_publisher'] . '</td>';
			$html .= '<td class="rel_browse_table">' . $result ['rel_result'] . '</td>';
			$html .= '<td class="rel_browse_table">' . $result ['rel_time'] . '</td>';
		}
		
		$this->getView ()->assign ( "html", $html );
	}
	
	public function getprojectAction()
	{
	    
	    $db_conn = new OmMysql ();
	    $row = $db_conn->mysql_query("release_sql.get_project", array());
	    header("content-type:application/json");
	    $result_array = array();
	    $res = array();
	    while($result = mysql_fetch_row($row))
	    {
	        //$res = explode(',', $result);
	        $result_array[] = $result;
	    }
	    
	    echo json_encode($result_array);
	    exit;
	
	}
	
	public function getprojectinfoAction()
	{
	    $project_info = explode(',', $_POST['project_name']);
	    $project_name = $project_info[0];
	    $project_region = $project_info[1];
	    $db_conn = new OmMysql ();
	    $row = $db_conn->mysql_query("release_sql.get_project_info", array("project_name"=>$project_name, "project_region"=>$project_region));
	    header("content-type:application/json");
	    $result = mysql_fetch_row($row);
	    echo (json_encode($result));
	    exit;
	
	}
	
	public function getbackupfilesAction()
	{
	    $project_name=$_POST['project_name'];
	    $ssh_conn = new OmSsh();
	    $backupfiles = sprintf("ls -1 %s",  Yaf_Application::app ()->getConfig ()->application->backup->base->path . "/" . $project_name);
	    try 
	    {
	        $ssh_conn -> ssh_connection_passwd();
	        $ssh_conn -> authPassword(Yaf_Application::app ()->getConfig ()->application->ssh->username, Yaf_Application::app ()->getConfig ()->application->ssh->password);
	        $result = $ssh_conn -> sshCmd_passwd_result($backupfiles);
	    } 
	    catch(Exception $e)
	    {
	        echo $e->getMessage();
	        return false;
	    }
	    $result_array=explode("\n", $result);
	    header("content-type:application/json");
	    echo (json_encode($result_array));
	    exit;
	
	}
	
	
	public function release_gitAction()
	{
		
	}
	
	/*
	 * 代码发布init
	 */
	private function release_init($project_name)
	{
	    
	    /*
	     * init--创建项目初始化目录
	     */
	    $this->TIME =  date('y-m-d-H-i-s',time());
	    $ssh_conn = new OmSsh();
	    $this->BASE_PATH = Yaf_Application::app ()->getConfig ()->application->base->path;
	    $this->CODE_BASE_PATH = Yaf_Application::app ()->getConfig ()->application->code->base->path;
	    $this->BACKUP_BASE_PATH = Yaf_Application::app ()->getConfig ()->application->backup->base->path;
	    $this->PACKAGE_BASE_PATH = Yaf_Application::app ()->getConfig ()->application->package->base->path;
	    $this->DEPLOY_BASE_PATH = Yaf_Application::app ()->getConfig ()->application->deploy->base->path;
	    
	    $judge_code_dir = sprintf("ls -1 %s | grep -w %s",  $this->BASE_PATH, "code");
	    $result = $ssh_conn ->sshCmd_try($judge_code_dir);
	    if(empty($result))
	    {
	        $make_dir_code = sprintf("mkdir -p %s", $this->CODE_BASE_PATH);
	        $ssh_conn ->sshCmd_try($make_dir_code);
	    }
	    
	    $judge_backup_dir = sprintf("ls -1 %s | grep -w %s",  $this->BASE_PATH, "backup");
	    $result = $ssh_conn ->sshCmd_try($judge_backup_dir);
	    if(empty($result))
	    {
	        $make_dir_backup = sprintf("mkdir -p %s", $this->BACKUP_BASE_PATH);
	        $ssh_conn ->sshCmd_try($make_dir_backup);
	    }
	    
	    $judge_package_dir = sprintf("ls -1 %s | grep -w %s",  $this->BASE_PATH, "package");
	    $result = $ssh_conn ->sshCmd_try($judge_package_dir);
	    if(empty($result))
	    {
	        $make_dir_package = sprintf("mkdir -p %s", $this->PACKAGE_BASE_PATH);
	        $ssh_conn ->sshCmd_try($make_dir_package);
	    }
	    
	    $this->PROJECT_CODE_PATH = $this->CODE_BASE_PATH . "/" . $project_name;
	    $this->PROJECT_BACKUP_PATH = $this->BACKUP_BASE_PATH . "/" . $project_name;
	    $this->PROJECT_PACKAGE_PATH = $this->PACKAGE_BASE_PATH . "/" . $project_name;
	    /*$PROJECT_GIT_URL = $_POST['project_method_url'];
	    $PROJECT_ECS_LIST = $_POST['project_ip'];*/
	    
	    /*$judge_project_code_dir = sprintf("ls -1 %s | grep %s",  $CODE_BASE_PATH, $_POST['project_name']);
	    $result = $ssh_conn ->sshCmd_try($judge_project_code_dir);
	    if(empty($result))
	    {
	        $make_dir_project_code = sprintf("mkdir -p %s", $PROJECT_CODE_PATH);
	        $ssh_conn ->sshCmd_try($make_dir_project_code);
	    }*/
	    
	    $judge_project_backup_dir = sprintf("ls -1 %s | grep -w %s",  $this->BACKUP_BASE_PATH, $project_name);
	    $result = $ssh_conn ->sshCmd_try($judge_project_backup_dir);
	    if(empty($result))
	    {
	        $make_dir_project_backup = sprintf("mkdir -p %s", $this->PROJECT_BACKUP_PATH);
	        $ssh_conn ->sshCmd_try($make_dir_project_backup);
	    }
	    
	    $judge_project_package_dir = sprintf("ls -1 %s | grep -w %s",  $this->PACKAGE_BASE_PATH, $project_name);
	    $result = $ssh_conn ->sshCmd_try($judge_project_package_dir);
	    if(empty($result))
	    {
	        $make_dir_project_package = sprintf("mkdir -p %s", $this->PROJECT_PACKAGE_PATH);
	        $ssh_conn ->sshCmd_try($make_dir_project_package);
	    }
	    
	    $project_backup_dir = $ssh_conn ->sshCmd_try($judge_project_backup_dir);
	    $project_package_dir = $ssh_conn ->sshCmd_try($judge_project_package_dir);
	    $code_base_dir = $ssh_conn ->sshCmd_try($judge_code_dir);
	    
	    if(empty($project_backup_dir) && empty($project_package_dir) && empty($code_base_dir))
	    {
	        return "init false";
	        exit;
	    }
	    else
	    {
	       return "0";    
	    }
	    
	}
	
	/*
	 * 代码发布init--java
	 */
	private function release_init_java($project_name)
	{
	    /*
	     * init--创建项目初始化目录
	     */
	    $this->TIME =  date('y-m-d-H-i-s',time());
	    $ssh_conn = new OmSsh();
	    $this->BASE_PATH = Yaf_Application::app ()->getConfig ()->application->base->path;
	    $this->CODE_BASE_PATH = Yaf_Application::app ()->getConfig ()->application->code->base->path;
	    $this->BACKUP_BASE_PATH = Yaf_Application::app ()->getConfig ()->application->backup->base->path;
	    $this->PACKAGE_BASE_PATH = Yaf_Application::app ()->getConfig ()->application->package->base->path;
	    $this->DEPLOY_BASE_PATH = Yaf_Application::app ()->getConfig ()->application->deploy->base->path;
	     
	    $judge_code_dir = sprintf("ls -1 %s | grep %s",  $this->BASE_PATH, "code");
	    $result = $ssh_conn ->sshCmd_java_try($judge_code_dir);
	    if(empty($result))
	    {
	        $make_dir_code = sprintf("mkdir -p %s", $this->CODE_BASE_PATH);
	        $ssh_conn ->sshCmd_java_try($make_dir_code);
	    }
	     
	    $judge_backup_dir = sprintf("ls -1 %s | grep %s",  $this->BASE_PATH, "backup");
	    $result = $ssh_conn ->sshCmd_java_try($judge_backup_dir);
	    if(empty($result))
	    {
	        $make_dir_backup = sprintf("mkdir -p %s", $this->BACKUP_BASE_PATH);
	        $ssh_conn ->sshCmd_java_try($make_dir_backup);
	    }
	     
	    $judge_package_dir = sprintf("ls -1 %s | grep %s",  $this->BASE_PATH, "package");
	    $result = $ssh_conn ->sshCmd_java_try($judge_package_dir);
	    if(empty($result))
	    {
	        $make_dir_package = sprintf("mkdir -p %s", $this->PACKAGE_BASE_PATH);
	        $ssh_conn ->sshCmd_java_try($make_dir_package);
	    }
	     
	    $this->PROJECT_CODE_PATH = $this->CODE_BASE_PATH . "/" . $project_name;
	    $this->PROJECT_BACKUP_PATH = $this->BACKUP_BASE_PATH . "/" . $project_name;
	    $this->PROJECT_PACKAGE_PATH = $this->PACKAGE_BASE_PATH . "/" . $project_name;
	    /*$PROJECT_GIT_URL = $_POST['project_method_url'];
	     $PROJECT_ECS_LIST = $_POST['project_ip'];*/
	     
	    /*$judge_project_code_dir = sprintf("ls -1 %s | grep %s",  $CODE_BASE_PATH, $_POST['project_name']);
	     $result = $ssh_conn ->sshCmd_try($judge_project_code_dir);
	     if(empty($result))
	     {
	     $make_dir_project_code = sprintf("mkdir -p %s", $PROJECT_CODE_PATH);
	     $ssh_conn ->sshCmd_try($make_dir_project_code);
	     }*/
	     
	    $judge_project_backup_dir = sprintf("ls -1 %s | grep %s",  $this->BACKUP_BASE_PATH, $project_name);
	    $result = $ssh_conn ->sshCmd_java_try($judge_project_backup_dir);
	    if(empty($result))
	    {
	        $make_dir_project_backup = sprintf("mkdir -p %s", $this->PROJECT_BACKUP_PATH);
	        $ssh_conn ->sshCmd_java_try($make_dir_project_backup);
	    }
	     
	    $judge_project_package_dir = sprintf("ls -1 %s | grep %s",  $this->PACKAGE_BASE_PATH, $project_name);
	    $result = $ssh_conn ->sshCmd_java_try($judge_project_package_dir);
	    if(empty($result))
	    {
	        $make_dir_project_package = sprintf("mkdir -p %s", $this->PROJECT_PACKAGE_PATH);
	        $ssh_conn ->sshCmd_java_try($make_dir_project_package);
	    }
	    
	    $project_backup_dir = $ssh_conn ->sshCmd_java_try($judge_project_backup_dir);
	    $project_package_dir = $ssh_conn ->sshCmd_java_try($judge_project_package_dir);
	    $code_base_dir = $ssh_conn ->sshCmd_java_try($judge_code_dir);
	    
	    if(empty($project_backup_dir) && empty($project_package_dir) && empty($code_base_dir))
	    {
	        return "init false";
	        exit;
	    }
	    else
	    {
	       return "0";    
	    }
	     
	}
	
	/*
	 * 代码发布backup
	 */
	private function release_backup($project_name)
	{
	    
	    /*
	     * backup--发布前备份代码
	     */
	    $ssh_conn = new OmSsh();
	    
	    $judge_project_code_dir_exist_codes = sprintf("ls -1 %s |grep -w %s",  $this->CODE_BASE_PATH, $project_name);
	    $result = $ssh_conn ->sshCmd_try($judge_project_code_dir_exist_codes);
	    if(empty($result))
	    {
	        return "0";
	    }
	    else
	    {
	        $targz_code = sprintf("cd %s && tar czvf %s/%s.%s.tar.gz *", $this->PROJECT_CODE_PATH, $this->PROJECT_BACKUP_PATH, $project_name, $this->TIME);
	        $ssh_conn ->sshCmd_try($targz_code);
	        //echo "backup end<br>";
	        $backup_file = sprintf("ls -1 %s |grep %s.%s.tar.gz", $this->PROJECT_BACKUP_PATH, $project_name, $this->TIME);
	        $file = $ssh_conn ->sshCmd_try($backup_file);
	        
	        if(trim($file) !== '')
	        {
	            return "0";
	        }
	        else
	        {
	            return "backup false";
	        }
	       
	    }
	}
	
	/*
	 * 代码发布backup--java
	 */
	private function release_backup_java($project_name)
	{  
	    /*
	     * backup--发布前备份代码
	     */
	    $ssh_conn = new OmSsh();
	     
	    $judge_project_code_dir_exist_codes = sprintf("ls -1 %s |grep %s",  $this->CODE_BASE_PATH, $project_name);
	    $result = $ssh_conn ->sshCmd_java_try($judge_project_code_dir_exist_codes);
	    if(empty($result))
	    {
	        //return "first time do not backup";
	        return "0";
	    }
	    else
	    {
	        $java_backup = sprintf("mv %s/%s.zip %s/%s.%s.zip", $this->PROJECT_PACKAGE_PATH, $project_name, $this->PROJECT_BACKUP_PATH, $project_name, $this->TIME);
	        $ssh_conn ->sshCmd_java_try($java_backup);
	        //echo "backup end<br>";
	        $backup_file = sprintf("ls -1 %s |grep %s.%s.zip", $this->PROJECT_BACKUP_PATH, $project_name, $this->TIME);
	        $file = $ssh_conn ->sshCmd_java_try($backup_file);

	        if(trim($file) !== '')
	        {
	           return "0";    
	        }
	        else
	        {
	           return "backup false";    
	        }
	    }
	}
	
	/*
	 * 检查备份文件只保修最近10个备份
	 */
	private function release_check_backup($project_name)
	{  
	    /*
	     * 检查备份文件
	     */
	    $ssh_conn = new OmSsh();
	     
	    $backupfiles_num = sprintf("ls -t %s |wc -l",  $this->PROJECT_BACKUP_PATH);
	    $num = $ssh_conn ->sshCmd_try($backupfiles_num);

	    if(trim($num) > 10)
	    {
	        $rm_num = $num - 10;
	        $rm_backup_files = sprintf("ls -t %s |tail -n %s",  $this->PROJECT_BACKUP_PATH, $rm_num);
	        $result = $ssh_conn ->sshCmd_try($rm_backup_files);
	        $rm_files_array = array_filter(explode("\n", $result));
	        for($i=0;$i<count($rm_files_array);$i++)
	        {
	            $rm = sprintf("rm -rf %s/%s", $this->PROJECT_BACKUP_PATH, $rm_files_array[$i]);
	            $ssh_conn ->sshCmd_try($rm);
	        }
	    }
	    return "0";
	}
	
	/*
	 * 检查备份文件只保修最近10个备份--java
	 */
	private function release_check_backup_java($project_name)
	{
	
	    /*
	     * 检查备份文件
	     */
	    $ssh_conn = new OmSsh();
	
	    $backupfiles_num = sprintf("ls -t %s |wc -l",  $this->PROJECT_BACKUP_PATH);
	    $num = $ssh_conn ->sshCmd_java_try($backupfiles_num);
	    if(trim($num) > 10)
	    {
	        $rm_num = $num - 10;
	        $rm_backup_files = sprintf("ls -t %s |tail -n %s",  $this->PROJECT_BACKUP_PATH, $rm_num);
	        $result = $ssh_conn ->sshCmd_java_try($rm_backup_files);
	        $rm_files_array = array_filter(explode("\n", $result));
	        for($i=0;$i<count($rm_files_array);$i++)
	        {
	            $rm = sprintf("rm -rf %s/%s", $this->PROJECT_BACKUP_PATH, $rm_files_array[$i]);
	            $ssh_conn ->sshCmd_java_try($rm);
	        }
	    }
	    return "0";
	}
	
	/*
	 * 代码发布update_code_git
	 */
	private function release_update_git($project_name)
	{
	    $ssh_conn = new OmSsh();
	    
	    $judge_project_code_dir_exist_codes = sprintf("ls -1 %s |grep -w %s",  $this->CODE_BASE_PATH, $project_name);
	    $result = $ssh_conn ->sshCmd_try($judge_project_code_dir_exist_codes);
	    
	    if(empty($result))
	    {   
	        $git_clone = sprintf("git clone %s %s", $this->PROJECT_GIT_URL, $this->PROJECT_CODE_PATH);
	        $ssh_conn ->sshCmd_try($git_clone);
	    }
	    $git_pull_master = sprintf("cd %s && git pull origin master", $this->PROJECT_CODE_PATH);
	    $ssh_conn ->sshCmd_try($git_pull_master);
	    $project_git = $ssh_conn ->sshCmd_try($git_pull_master);
	    
	    if(strstr($project_git, 'Already up-to-date.') == false)
	    {
	        return "project update false";
	        exit;
	    }
	    else
	    {
	        return "0";
	    }
	}
	
	/*
	 * 代码发布update_code_git_java
	 */
	private function release_update_git_java($project_name)
	{
	    $ssh_conn = new OmSsh();
	    
	    $judge_autoconfig_dir_exist = sprintf("ls -1 %s |grep autoconfig",  $this->CODE_BASE_PATH);
	    $ssh_conn ->sshCmd_java_try($judge_autoconfig_dir_exist);
	    //echo $result;
	    if(empty($result))
	    {
	        $git_clone_autoconfig = sprintf("bin/git clone http://readme:yoho9646@git.dev.yoho.cn/yohoops/deploy.git %s/autoconfig", $this->CODE_BASE_PATH);
	        $result = $ssh_conn ->sshCmd_java_try($git_clone_autoconfig);
	    }
	    
	    
	    $judge_project_code_dir_exist_codes = sprintf("ls -1 %s |grep %s",  $this->CODE_BASE_PATH, $project_name);
	    $result = $ssh_conn ->sshCmd_java_try($judge_project_code_dir_exist_codes);
	    if(empty($result))
	    {
	        $git_clone = sprintf("bin/git clone %s %s", $this->PROJECT_GIT_URL, $this->PROJECT_CODE_PATH);
	        $ssh_conn ->sshCmd_java_try($git_clone);
	    }
	    
	    $git_pull_autoconfig = sprintf("cd %s/autoconfig && ~/bin/git pull origin master", $this->CODE_BASE_PATH);
	    $git_pull_master = sprintf("cd %s && ~/bin/git pull origin master", $this->PROJECT_CODE_PATH);
	    $autoconfig = $ssh_conn ->sshCmd_java_try($git_pull_autoconfig);
	    $project_git = $ssh_conn ->sshCmd_java_try($git_pull_master);
	    
	    if(strstr($autoconfig, 'Already up-to-date.') == false)
	    {
	       return "autoconfig update false";
	       exit;
	    }
	    if(strstr($project_git, 'Already up-to-date.') == false)
	    {
	        return "project update false";
	        exit;
	    }
	    return "0";
	}
	
	/*
	 * 代码发布package
	 */
	private function release_package($project_name)
	{
	    $ssh_conn = new OmSsh(); 
	    $package = sprintf("cd %s && tar czvf %s/%s.tar.gz *", $this->PROJECT_CODE_PATH, $this->PROJECT_PACKAGE_PATH, $project_name );
	    $ssh_conn ->sshCmd_try($package);
	    
	    $package_file = sprintf("ls -1 %s |grep -w %s.tar.gz", $this->PROJECT_PACKAGE_PATH, $project_name);
	    $file = $ssh_conn ->sshCmd_try($package_file);
	    
	    if(trim($file) !== '')
	    {
	        return "0";
	    }
	    else
	    {
	        return "package false";
	    }
	}
	
	/*
	 * 代码发布package--java
	 */
	private function release_package_java($project_name)
	{
	    $ssh_conn = new OmSsh();
	    $package = sprintf("cd %s/bin/ && sh %s/bin/mvn-package.sh %s/autoconfig/autoconfig/master/%s-autoconfig.properties %s/autoconfig/autoconfig/master/global-autoconfig.properties", 
	                        $this->PROJECT_CODE_PATH, $this->PROJECT_CODE_PATH, $this->CODE_BASE_PATH, $project_name, $this->CODE_BASE_PATH);   
	    $package_result = $ssh_conn ->sshCmd_java_try($package);
	    $result = trim($package_result);
	   
	    $mv = sprintf("mv %s/deploy/target/%s.zip %s/%s.zip", $this->PROJECT_CODE_PATH, $project_name, $this->PROJECT_PACKAGE_PATH, $project_name);
	    $ssh_conn ->sshCmd_java_try($mv);
	    
	    $package_file = sprintf("ls -1 %s |grep %s.zip", $this->PROJECT_PACKAGE_PATH, $project_name);
	    $file = $ssh_conn ->sshCmd_java_try($package_file);
	   
	    if(strstr($package_result,"BUILD SUCCESS") == false)
	    {
	        return "package false";
	        exit;
	    }
	    
	    if(trim($file) == '')
	    {
	        return "package--mv false";
	        exit;
	    }
	    return "0";
	    
	    
	}
	
	/*
	 * 代码发布rsync
	 */
	private function release_sync($project_name)
	{
	    /*
	     * sync--代码同步到线上服务器
	     */
	    $ssh_conn = new OmSsh();
	    $array_ips = explode("<br>", $this->PROJECT_ECS_LIST);
	    for($i=0;$i<count($array_ips);$i++)
	    {
	        $sync = sprintf("scp %s/%s.tar.gz %s:/tmp", $this->PROJECT_PACKAGE_PATH, $project_name, $array_ips[$i]);
	        $ssh_conn ->sshCmd_try($sync);
	        
	        $ls_code = sprintf("ssh %s \"ls -1 /tmp |grep %s.tar.gz\"", $array_ips[$i], $project_name);
	        $result_ls_code = $ssh_conn ->sshCmd_try($ls_code);
	        if(trim($result_ls_code) == '' )
	        {
	            return $array_ips[$i] . "sync code false";
	            exit;
	        }
	    }
	    return "0";
	}
	
	/*
	 * 代码发布rsync--java
	 */
	private function release_sync_java($project_name, $project_code_path)
	{
	    /*
	     * sync--代码同步到线上服务器--java
	     */
	    $ssh_conn = new OmSsh();
	    $array_ips = explode("<br>", $this->PROJECT_ECS_LIST);
	    for($i=0;$i<count($array_ips);$i++)
	    {
	        $sync = sprintf("scp %s/%s.zip %s:%s/%s.zip", $this->PROJECT_PACKAGE_PATH, $project_name, $array_ips[$i], $project_code_path, $project_name);
	        $sync_function = sprintf("scp %s/autoconfig/scripts/function.sh %s:%s/", $this->CODE_BASE_PATH, $array_ips[$i], $project_code_path);
	        
	        $sync_code = $ssh_conn ->sshCmd_java_try($sync);
	        $sync_shell = $ssh_conn ->sshCmd_java_try($sync_function);
	        
	        $ls_code = sprintf("ssh %s \"ls -1 %s |grep %s.zip\"", $array_ips[$i], $project_code_path, $project_name);
	        $ls_function = sprintf("ssh %s \"ls -1 %s | grep function.sh\"", $array_ips[$i], $project_code_path);
	        $result_ls_code = $ssh_conn ->sshCmd_java_try($ls_code);
	        $result_ls_function = $ssh_conn ->sshCmd_java_try($ls_function);

	        if(trim($result_ls_code) == '' )
	        {
	            return $array_ips[$i] . "sync code false";
	            exit;
	        }
	        else
	        {
	            if(trim($result_ls_function) == '')
	            {
	                return $array_ips[$i] . "sync function.sh false";
	                exit;
	            }
	        }
	    }
	    return "0";
	}
	
	/*
	 * 代码发布deploy
	 */
	private function release_deploy($project_name, $project_code_path)
	{
	    /*
	     * deploy--代码部署
	     */
	    $ssh_conn = new OmSsh();
	    $array_ips = explode("<br>", $this->PROJECT_ECS_LIST);
	    for($i=0;$i<count($array_ips);$i++)
	    {
	        $deploy = sprintf("ssh  %s \"sudo chown om -R %s && tar zxvf /tmp/%s.tar.gz -C %s && rm -rf /tmp/%s.tar.gz\"", $array_ips[$i], $project_code_path, $project_name, $project_code_path, $project_name);
	        $ssh_conn ->sshCmd_try($deploy);
	    }
	    
	    return "0";
	}
	
	/*
	 * 代码发布deploy--java
	 */
	private function release_deploy_java($project_name, $project_code_path, $nginx_ip, $nginx_conf)
	{
	   
	    /*
	     * deploy--代码部署
	     */
	    $ssh_conn = new OmSsh();
	    $array_ips = explode("<br>", $this->PROJECT_ECS_LIST);
	    $array_nginx_ips = explode("<br>", $nginx_ip);
	    
	    if ($project_name == 'yoho-gateway')
	    {
	       for ($i = 0; $i < count($array_ips); $i ++) 
	       {
                /*
                 * 注释nginx upstream配置
                 */
	            
                for ($j = 0; $j < count($array_nginx_ips); $j ++) 
                {
                    /*
                     * 注释nginx中的upstream配置
                     */
                    $upstream = sprintf("ssh %s \"sudo sed -i '/%s/ s/^/#/' %s\"", $array_nginx_ips[$j], $array_ips[$i], $nginx_conf);
                    $ssh_conn->sshCmd_java_try($upstream);
                    
                    $check_upstream = sprintf("ssh %s \"sudo sed -n '/%s/p' %s | grep '#'\"", $array_nginx_ips[$j], $array_ips[$i], $nginx_conf);
                    $check_upstream_result = $ssh_conn->sshCmd_java_try($check_upstream);
                    if (trim($check_upstream_result) == '')
                    {
                        return $array_nginx_ips[$j] . "nginx upstream add # false";
                        exit;
                    }
                    else
                    {
                        /*
                         * 重启nginx
                         */
                        $nginx_reload = sprintf("ssh %s \"sudo /usr/local/nginx/sbin/nginx -s reload\"", $array_nginx_ips[$j]);
                        $ssh_conn->sshCmd_java_try($nginx_reload); 
                        
                    }
                }
                
                /*
                 * 等待10秒
                 */
                sleep(10);
                /*
                 * java代码部署
                 */
                $function = sprintf("ssh  %s \"sh %s/function.sh /home/master %s\"", $array_ips[$i], $project_code_path, $project_name);
                $function_result = $ssh_conn->sshCmd_java_try($function);
                
                if (strstr($function_result, "startup web server done!") == false) 
                {
                    return $array_ips[$i] . ":Restart tomcat false";
                    exit();
                }
                
                
                
                /*
                 * 重新打开nginx upstream
                 */
                for ($j = 0; $j < count($array_nginx_ips); $j ++) 
                {
                    /*
                     * 打开nginx中的upstream配置
                     */
                    $upstream = sprintf("ssh %s \"sudo sed -i '/%s/ s/#//' %s\"", $array_nginx_ips[$j], $array_ips[$i], $nginx_conf);
                    $ssh_conn->sshCmd_java_try($upstream);
                    
                    $check_upstream = sprintf("ssh %s \"sudo sed -n '/%s/p' %s | grep '#'\"", $array_nginx_ips[$j], $array_ips[$i], $nginx_conf);
                    $check_upstream_result = $ssh_conn->sshCmd_java_try($check_upstream);
                    while (trim($check_upstream_result) !== '') 
                    {
                        $ssh_conn->sshCmd_java_try($upstream);
                    }
                    /*
                     * 重启nginx
                     */
                    $nginx_reload = sprintf("ssh %s \"sudo /usr/local/nginx/sbin/nginx -s reload\"", $array_nginx_ips[$j]);
                    $ssh_conn->sshCmd_java_try($nginx_reload);
                    
                }
                 
            }
            return "0";
	    }
	    else 
	    {
	        //$GW="http://192.168.74.6:8080/gateway";
	        for ($i = 0; $i < count($array_ips); $i ++)
	        {
	            /*
	             * 从zookper中将服务器剔除
	             */
	            $REMOVE_URL=$GW . "/service_control/unregister?ip=" . $array_ips[$i] . "&context=\${Appname_Contexts[\"" . $project_name . "\"]}";
	            $curl = sprintf("curl -m 10 -s -o /dev/null \"%s\"", $REMOVE_URL);
	            /*
	             * 等待10秒
	             */
	            sleep(10);
	            /*
	             * java代码部署
	             */
	            $function = sprintf("ssh  %s \"sh /home/master/%s/function.sh /home/master %s\"", $array_ips[$i], $project_name, $project_name);
                $function_result = $ssh_conn->sshCmd_java_try($function);

                if(strstr($function_result,"startup web server done!") == false)
                {
                    return $array_ips[$i] . ":Restart tomcat false";
                    exit;
                }
	        }  
	    }
	    //echo "deploy end<br>";
	    return "0";
	}
	
	/*
	 * 代码发布修改代码所属权限
	 */
	private function release_chown($project_code_path)
	{
	    //echo "chown start<br>";
	    /*
	     * deploy--代码部署
	     */
	    $ssh_conn = new OmSsh();
	    $array_ips = explode("<br>", $this->PROJECT_ECS_LIST);
	    for($i=0;$i<count($array_ips);$i++)
	    {
	        $chown = sprintf("ssh %s \"sudo chown www.www -R %s/* \"", $array_ips[$i], $project_code_path);
	        $ssh_conn ->sshCmd_try($chown);
	    }
	    return "0";
	}
	
	/*
	 * 代码发布rollback,拷贝备份-->发布文件
	 */
	private function rollback_cp($project_name, $project_backup_file)
	{
	    //echo "rollback_cp  start<br>";
	    /*
	     * cp--将备份文件copy到打包文件
	     */
	    $ssh_conn = new OmSsh();
	    $cp = sprintf("rm -rf %s/%s.tar.gz && cp %s/%s %s/%s.tar.gz", $this->PROJECT_PACKAGE_PATH, $project_name, $this->PROJECT_BACKUP_PATH, $project_backup_file, $this->PROJECT_PACKAGE_PATH, $project_name);
	    $ssh_conn ->sshCmd_try($cp);
	    $cp_result = $ssh_conn ->sshCmd_try($cp);
	    if(trim($cp_result) !== '')
	    {
	        return "recovery from backup false";
	    }
	    else
	    {
	        return "0";
	    }
	}
	
	/*
	 * 代码发布rollback,拷贝备份-->发布文件 --java
	 */
	private function rollback_cp_java($project_name, $project_backup_file)
	{
	    //echo "rollback_cp  start<br>";
	    /*
	     * cp--将备份文件copy到打包文件
	     */
	    $ssh_conn = new OmSsh();
	    $cp = sprintf("rm -rf %s/%s.zip && cp %s/%s %s/%s.zip", $this->PROJECT_PACKAGE_PATH, $project_name, $this->PROJECT_BACKUP_PATH, $project_backup_file, $this->PROJECT_PACKAGE_PATH, $project_name);
	    $cp_result = $ssh_conn ->sshCmd_java_try($cp);
	    if(trim($cp_result) !== '')
	    {
	        return "recovery from backup false";
	    }
	    else
	    {
	        return "0";
	    }
	}
	
	
	/*
	 * 发布action
	 */
	public function release_actionAction()
	{
	   set_time_limit(3600);
	   if ($_POST['project_name'] == 'choose')
	   {
	       echo "choose project";
	       exit;
	   }
	   /*
	    * 发布动作
	    */
	   if ($_POST['rel_action'] == 'release')
	   {
	       $project_name = $_POST['project_name'];
	       if (empty($project_name))
	       {
	           echo "project_name not null";
	           exit;
	       }
	       $this->PROJECT_GIT_URL = $_POST['project_method_url'];
	       $this->PROJECT_ECS_LIST = $_POST['project_ip'];
	       $project_code_path = $_POST['project_code_path'];
	       $nginx_ip = $_POST['project_nginx_ip'];
	       $nginx_conf = $_POST['project_nginx_conf'];
	       $GW = $_POST['project_zookeeper'];

	       /*
	        * php or java
	        */
	       $language = $_POST['project_language'];
	       
	       /*
	        * php发布
	        */
	       if ($language == 'php')
	       {
                    
                /*
                 * 初始化参数
                 */
                if ($this->release_init($project_name) !== '0') 
	            {
	                echo $this->release_init($project_name);
	                exit();
	            }
	            
                /*
                 * 备份
                 */
                if ($this->release_backup($project_name) !== '0')
	            {
	                echo $this->release_backup($project_name);
	                exit;
	            }
	            //echo "backup";
                /*
                 * 检查备份文件是否大于10个，如果大于删除多余的备份
                 */
                if ($this->release_check_backup($project_name) !== '0')
                {
                    echo $this->release_check_backup($project_name);
                    exit;
                }
                //echo "check backup";
                /*
                 * git拉取代码
                 */
                if ($this->release_update_git($project_name) !== '0')
	            {
	                echo $this->release_update_git($project_name);
	                exit();
	            }
	            //echo "git";
                /*
                 * 打包
                 */
                if ($this->release_package($project_name) !== '0') 
                {
                    echo $this->release_package($project_name);
                    exit();
                }
                
                /*
                 * 上传生产环境服务器
                 */
                if ($this->release_sync($project_name) !== '0') 
	            {
	                echo $this->release_sync($project_name);
	                exit();
	            }
	            
                /*
                 * 覆盖代码
                 */
                if ($this->release_deploy($project_name, $project_code_path) !== '0') 
                {
                    echo "deploy false";
                    exit();
                }
                
                /*
                 * 修改代码目录权限
                 */
                if ($this->release_chown($project_code_path) !== '0') 
                {
                    echo "chown false";
                    exit();
                }
                             
                /*
                 * 发布记录写入数据库
                 */
                $db_conn = new OmMysql ();
                $row = $db_conn->mysql_query ( "release_sql.record_release", array("rel_app"=>$project_name, "rel_publisher"=>$_SESSION['username'], "rel_result"=>"release"));
	            if(!$row)
			    {
				    die('Error: ' . mysql_error());
			    }
                
                /*
                 * 全部成功返回success
                 */
                echo "success";
	       }
	       if ($language == 'java')
	       {
	           /*
	            * 初始化参数
	            */
	           if ($this->release_init_java($project_name) !== '0')
	           {
	               echo $this->release_init_java($project_name);
	               exit();
	           }
	           
	           /*
	            * 备份
	            */
	           if ($this->release_backup_java($project_name) !== '0')
	           {
	               echo $this->release_backup_java($project_name);
	               exit;
	           }
	           
	           /*
	            * 检查备份文件是否大于10个，如果大于删除多余的备份
	            */
	           if ($this->release_check_backup_java($project_name) !== '0')
	           {
	               echo "check_backup_files false";
	               exit();
	           }
	           /*
	            * git拉取代码
	            */
	           if ($this->release_update_git_java($project_name) !== '0')
	           {
	               if ($this->release_update_git_java($project_name) == 'autoconfig update false')
	               {
	                   echo "autoconfig update false";
	                   exit(); 
	               }
	               if ($this->release_update_git_java($project_name) == 'project update false')
	               {
	                   echo "project update false";
	                   exit();
	               }
	           }
	           
	           /*
	            * java打包
	            */
	           if ($this->release_package_java($project_name) !== '0')
	           {
	               if ($this->release_package_java($project_name) == 'mv false') 
	               {
                        echo "mv false";
                        exit();
                   }
                   if ($this->release_package_java($project_name) == 'package false') 
                   {
                        echo "package false";
                        exit();
                   }
	           }
	           
	           /*
	            * 上传生产环境服务器
	            */
	           if ($this->release_sync_java($project_name, $project_code_path) !== '0')
	           {
	               echo $this->release_sync_java($project_name, $project_code_path);
	               exit();
	           }
	           
	           /*
	            * 覆盖代码
	            */
	           if ($this->release_deploy_java($project_name, $project_code_path, $nginx_ip, $nginx_conf) !== '0')
	           {
	               echo $this->release_deploy_java($project_name, $project_code_path, $nginx_ip, $nginx_conf);
	               exit();
	           }
	           echo "success";
	       }
	   }
	   if ($_POST['rel_action'] == 'rollback')
	   {
	       
	       $language = $_POST['project_language'];
	       $this->PROJECT_GIT_URL = $_POST['project_method_url'];
	       $this->PROJECT_ECS_LIST = $_POST['project_ip'];
	       $project_code_path = $_POST['project_code_path'];
	       $nginx_ip = $_POST['project_nginx_ip'];
	       $nginx_conf = $_POST['project_nginx_conf'];
	       $GW = $_POST['project_zookeeper'];
	       $project_name = $_POST['project_name'];
	       $project_backup_file = $_POST["backup_files"];
	       /*
	        * php回滚
	       */
	       if ($language == 'php')
	       {
                $project_name = $_POST['project_name'];
                $project_backup_file = $_POST["backup_files"];
                if ($project_backup_file == 'choose') 
                {
                    echo "choose backup file";
                    exit();
                }
                $project_code_path = $_POST['project_code_path'];
                $this->PROJECT_ECS_LIST = $_POST['project_ip'];
                
                /*
                 * 初始化参数
                 */
	            if ($this->release_init($project_name) !== '0') 
	            {
	                echo "init false";
	                exit();
	            }
                /*
                 * 选择的备份文件生成要发布文件包
                 */
                if ($this->rollback_cp($project_name, $project_backup_file) !== '0') 
                {
                    echo "rollback_cp false";
                    exit();
                }
                /*
                 * 上传生产环境服务器
                 */
                if ($this->release_sync($project_name) !== '0') 
	            {
	                echo $this->release_sync($project_name);
	                exit();
	            }
                /*
                 * 覆盖代码
                 */
                if ($this->release_deploy($project_name, $project_code_path) !== '0') 
                {
                    echo "deploy false";
                    exit();
                }
                /*
                 * 修改代码目录权限
                 */
                if ($this->release_chown($project_code_path) !== '0') 
                {
                    echo "chown false";
                    exit();
                }
                
                /*
                 * 发布记录写入数据库
                 */
                $db_conn = new OmMysql ();
                $row = $db_conn->mysql_query ( "release_sql.record_release", array("rel_app"=>$project_name, "rel_publisher"=>$_SESSION['username'], "rel_result"=>"rollback"));
                if(!$row)
                {
                    die('Error: ' . mysql_error());
                }
                
                echo "rollback success";
	       }
	       if ($language == 'java')
	       {
	           if ($project_backup_file == 'choose')
	           {
	               echo "choose backup file";
	               exit();
	           }
	           /*
	            * 初始化参数
	            */
	           if ($this->release_init_java($project_name) !== '0')
	           {
	               echo $this->release_init_java($project_name);
	               exit();
	           }
	           /*
	            * 选择的备份文件生成要发布文件包
	            */
	           if ($this->rollback_cp_java($project_name, $project_backup_file) !== '0')
	           {
	               echo $this->rollback_cp_java($project_name, $project_backup_file);
	               exit();
	           }
	           /*
	            * 上传生产环境服务器
	            */
	           if ($this->release_sync_java($project_name, $project_code_path) !== '0')
	           {
	               echo $this->release_sync_java($project_name, $project_code_path);
	               exit();
	           }
	           
	           /*
	            * 覆盖代码
	            */
	           if ($this->release_deploy_java($project_name, $project_code_path, $nginx_ip, $nginx_conf) !== '0')
	           {
	               echo $this->release_deploy_java($project_name, $project_code_path, $nginx_ip, $nginx_conf);
	               exit();
	           }
	           echo "rollback success";
	       }
	   }   
	   return false;
	   
	}
	
	/*
	 * svn发布
	 */
	public function release_svnAction()
	{
		//$this->getView()->assign("content");
		session_start();
		$step = $_REQUEST['step'];
		if(empty($step)){
			$step = 1;
		}
		switch($step){
			case 2:
				$_SESSION['rel_method'] = $_POST['rel_method'];
				$_SESSION['rel_code'] = $_POST['rel_code'];
				$_SESSION['rel_developer'] = $_POST['rel_developer'];
				$_SESSION['rel_app'] = $_POST['rel_app'];
				$_SESSION['rel_qa'] = $_POST['rel_qa'];
				$_SESSION['rel_publisher'] = $_POST['rel_publisher'];
				
				$rel_svn_app_array = array(yohocn=>array(0=>'YOHOCN', 1=>'/Data/yoho.cn_code/www.yoho.cn/'),
										  om=>array(0=>'om_svn', 1=>'/Data/PE/om_svn/om.yaf.svn.yoho.cn/'));
				$rel_svn_app_info = $rel_svn_app_array[$_SESSION['rel_app']];
				
				$rel_svn_codes_array = array();
				foreach (array_filter(explode("\n",trim($_SESSION['rel_code']))) as $key => $value)
				{
					$value = trim($value);
					if(!empty($value))
					{
						array_push($rel_svn_codes_array,$rel_svn_app_info[1] . $value);
					}
				}
				/*将需要更新的文件，写入服务器版本地文件  */
				$file = fopen(Yaf_Application::app ()->getConfig ()->application->svn->file->path ,"w");
				$codes = implode("\n",$rel_svn_codes_array);
				//echo $codes;
				fwrite($file,$codes);
				fclose($file);

				$this->getView()->display('release/release/svn/svn_step2.phtml', array("rel_app"=>$_SESSION['rel_app']));
				return false;
				break;
			case 3:
				$_SESSION['rel_syncserver_name'] = $_POST['rel_syncserver_name'];
				$_SESSION['rel_syncserver_passwd'] = $_POST['rel_syncserver_passwd'];
				/*
				 * ssh连接
				*/
				$ssh_conn = new OmSsh();
				$svn_up = sprintf("svn up $(cat %s)", Yaf_Application::app ()->getConfig ()->application->svn->file->path);
				try {
					$ssh_conn -> ssh_connection_passwd();
					$ssh_conn -> authPassword($_POST['rel_syncserver_name'], $_POST['rel_syncserver_passwd']);
					$result = $ssh_conn -> sshCmd_passwd_result($svn_up);
				}
				catch(Exception $e)
				{
					echo $e->getMessage();
					return false;
				}
				$svn_result = str_replace("\n", "<br>", $result);
				$_SESSION['svn_result'] = $svn_result;
	
				$this->getView()->display('release/release/svn/svn_step3.phtml', array("result"=>$svn_result));
				return false;
				break;
			case 4:
				$this->getView()->display('release/release/svn/svn_step4.phtml', array("rel_app"=>$_SESSION['rel_app']));
				return false;
				break;
			case 5:
				/*
				 * 分割需要检查的服务器ip
				*/
				$check_list = explode ( ',', $_POST ['rel_check_ip'] );
				/*
				 * 依次检查这些IP下的，更新的文件
				*/
				for($i=0; $i<count($check_list); $i++) {
					$ssh_conn = new OmSsh();
					try {
						$ssh_conn -> ssh_connection_passwd();
						$ssh_conn -> authPassword($_SESSION['rel_syncserver_name'], $_SESSION['rel_syncserver_passwd']);
						$check_release = sprintf("sudo ssh %s ls -l $(cat %s)", $check_list[$i],
								Yaf_Application::app ()->getConfig ()->application->svn->file->path);
						$result[] = $ssh_conn -> sshShell_passwd_result($check_release);
					}
					catch(Exception $e)
					{
						echo $e->getMessage();
						return false;
					}
				}
				$_SESSION['svn_check_result'] = $result;
				$this->getView()->display('release/release/svn/svn_step5.phtml', array('result' => $result));
				return false;
				break;
			CASE 6:
				/*
				 * 记录结果写入数据库
					*/
				$db_conn = new OmMysql ();
				$row = $db_conn->mysql_query("release_sql.svn_release", array('rel_app'=>$_SESSION['rel_app'] ,
						'rel_developer'=>$_SESSION['rel_developer'], 'rel_qa'=>$_SESSION['rel_qa'],
						'rel_publisher'=>$_SESSION['rel_publisher'], 'rel_result'=>$_SESSION['svn_result'],
						'rel_check_result'=>str_replace("\n", "<br>",implode(",", $_SESSION['svn_check_result'])),
						'rel_method'=>$_SESSION['rel_method']));
				if(!$row)
				{
					die('Error: ' . mysql_error());
				}
				echo '<script type="text/javascript">window.onload=function(){alert("sucess");window.top.location.href="/release/releasecode";}</script>';
				exit;
		}
	}
	public function release_detailAction()
	{
		$db_conn = new OmMysql ();
		$id = $_POST['id'];
		//echo $id;
		$row = $db_conn->mysql_query("release_sql.release_history_detail", array('id'=> $id));
		
		header("content-type:application/json");
		$result = mysql_fetch_assoc($row);
		echo (json_encode($result));
		return false;
	}
	
	/*
	 * DNS修改
	 */
	public function dnsAction()
	{
	     
	}
	
	public function dns_awsAction()
	{
	    if (!empty($_GET['domain']))
	    {
	        
	        $db_conn = new OmMysql ();
	        $row = $db_conn->mysql_query("release_sql.search_dns_record", array('dns_domain'=> $_GET['domain'], 
	                                                                            'dns_cloud' => $_GET['source']));
	        //$result_array = array();
	        $html = '';
	        $info = '';
	        $info .= '<input type="hidden" id="domain" name="domain" class="rel_record_form" value="' . $_GET['domain'] . '">';
	        $info .= '<input type="hidden" id="source" name="source" class="rel_record_form" value="' . $_GET['source'] . '">';
	        while($result = mysql_fetch_array($row))
	        {
	            //$result_array[] = $result;
	            $html .= '<tr id="' . $result ['id'] . '">';
	            $html .= '<td id="dns_record" class="" >' . $result ['dns_record'] . '</td>';
	            $html .= '<td id="dns_type" class="">' . $result ['dns_type'] . '</td>';
	            $html .= '<td id="dns_value" class="" >' . $result ['dns_value'] . '</td>';
	            $html .= '<td><input id="edit" type="button" class="edit btn btn-warning" value="Edit">
	                         <input id="del" type="button" class="del btn btn-danger" value="Del"></td>';
	        }
	        $this->getView ()->assign ( "info", $info );
	        $this->getView ()->assign ( "html", $html );
	        $this->getView()->display('release/dns/record.phtml');
	        exit;
	    }
	    
	}
	
	public function dns_qcloudAction()
	{
	    if (!empty($_GET['domain']))
	    {
	         
	        $db_conn = new OmMysql ();
	        $row = $db_conn->mysql_query("release_sql.search_dns_record", array('dns_domain'=> $_GET['domain'],
	            'dns_cloud' => $_GET['source']));
	        //$result_array = array();
	        $html = '';
	        $info = '';
	        $info .= '<input type="hidden" id="domain" name="domain" class="rel_record_form" value="' . $_GET['domain'] . '">';
	        $info .= '<input type="hidden" id="source" name="source" class="rel_record_form" value="' . $_GET['source'] . '">';
	        while($result = mysql_fetch_array($row))
	        {
	            //$result_array[] = $result;
	            $html .= '<tr id="' . $result ['id'] . '">';
	            $html .= '<td id="dns_record" class="" >' . $result ['dns_record'] . '</td>';
	            $html .= '<td id="dns_type" class="">' . $result ['dns_type'] . '</td>';
	            $html .= '<td id="dns_value" class="" >' . $result ['dns_value'] . '</td>';
	            $html .= '<td><input id="edit" type="button" class="edit btn btn-warning" value="Edit">
	                         <input id="del" type="button" class="del btn btn-danger" value="Del"></td>';
	        }
	        $this->getView ()->assign ( "info", $info );
	        $this->getView ()->assign ( "html", $html );
	        $this->getView()->display('release/dns/record.phtml');
	        exit;
	    }
	     
	}
	
	/*
	 * 更新本地DNS记录
	 */
	public function update_dnsAction()
	{
	    //echo $_POST['id'];
	    //echo $_POST['dns_record'];
	    //echo $_POST['dns_type'];
	    //echo $_POST['dns_value'];
	    $db_conn = new OmMysql ();
	    $row = $db_conn->mysql_query("release_sql.update_dns", array('dns_record'=> $_POST['dns_record'],
	                                                                 'dns_type'=> $_POST['dns_type'],
	                                                                 'dns_value'=> $_POST['dns_value'],
	                                                                 'id'=> $_POST['id']));
	    
	    if(!$row)
	    {
			die('Error: ' . mysql_error());
		}
		else
		{
	       echo "success";
		}
	    return false;
	}
	
	/*
	 * 删除本地DNS记录
	 */
	public function del_dnsAction()
	{
	    //echo $_POST['id'];
	    //echo $_POST['dns_record'];
	    //echo $_POST['dns_type'];
	    //echo $_POST['dns_value'];
	    $db_conn = new OmMysql ();
	    $row = $db_conn->mysql_query("release_sql.del_dns", array('id'=> $_POST['id']));
	     
	    if(!$row)
	    {
	        die('Error: ' . mysql_error());
	    }
	    else
	    {
	        echo "success";
	    }
	    return false;
	}
	
	/*
	 * 添加本地DNS记录
	 */
	public function add_dnsAction()
	{
	    $record = $_POST['record'];
	    $type = $_POST['type'];
	    $value = $_POST['value'];
	    $domain = $_POST['domain'];
	    $cloud = $_POST['cloud'];
	    
	    
	    foreach ($record as $k => $v)
	    {
	        $dns_record = mysql_real_escape_string($v);
	        $dns_type = mysql_real_escape_string($type[$k]);
	        $dns_value = mysql_real_escape_string($value[$k]);
	        $dns_domain = mysql_real_escape_string($domain[$k]);
	        $dns_cloud = mysql_real_escape_string($cloud[$k]);
	    
	        $db_conn = new OmMysql ();
	        $row = $db_conn->mysql_query("release_sql.add_dns", array('dns_record'=> $dns_record,'dns_type' => $dns_type,
	            'dns_value'=> $dns_value,'dns_domain'=> $dns_domain,'dns_cloud'=> $dns_cloud));
	        if(!$row)
	        {
	            die('Error: ' . mysql_error());
	        }
	        else
	        {
	            echo '<script type="text/javascript">window.onload=function(){alert("sucess");window.top.location.href="http://om.yoho.cn/release/dns_aws?domain=' . $dns_domain . '&source=' . $dns_cloud . '";}</script>';
	        }
	    }
	}
	
	/*
	 * DNS发布初始化
	 */
	private function release_dns_init($domain, $cloud)
	{
	     
	    /*
	     * init--创建项目初始化目录
	     */
	    $this->TIME =  date('y-m-d-H-i-s',time());
	    $this->DNS_TIME =  date('ymdHi',time());

	    $ssh_conn = new OmSsh();
	    $this->DNS_BACKUP_PATH = Yaf_Application::app ()->getConfig ()->application->dns->backup->path;
	    $this->DNS_PACKAGE_PATH = Yaf_Application::app ()->getConfig ()->application->dns->package->path;
	   
	     
	    $judge_backup_dir = sprintf("ls -1 %s | grep -w %s",  $this->DNS_BACKUP_PATH, $cloud);
	    $result = $ssh_conn ->sshCmd_try($judge_backup_dir);
	    if(empty($result))
	    {
	        $make_dir_backup = sprintf("mkdir -p %s", $this->DNS_BACKUP_PATH . "/" . $cloud);
	        $ssh_conn ->sshCmd_try($make_dir_backup);
	        $chmod_backup_dir = sprintf("sudo chmod o+w %s", $this->DNS_BACKUP_PATH . "/" . $cloud);
	        $ssh_conn ->sshCmd_try($chmod_backup_dir);
	    }
	     
	    $judge_package_dir = sprintf("ls -1 %s | grep -w %s",  $this->DNS_PACKAGE_PATH, $cloud);
	    $result = $ssh_conn ->sshCmd_try($judge_package_dir);
	    if(empty($result))
	    {
	        $make_dir_package = sprintf("mkdir -p %s", $this->DNS_PACKAGE_PATH . "/" . $cloud);
	        $ssh_conn ->sshCmd_try($make_dir_package);
	        $chmod_package_dir = sprintf("sudo chmod o+w %s", $this->DNS_PACKAGE_PATH . "/" . $cloud);
	        $ssh_conn ->sshCmd_try($chmod_package_dir);
	    }
	     
	    $this->DNS_DOMAIN_BACKUP_PATH = $this->DNS_BACKUP_PATH . "/" . $cloud;
	    $this->DNS_DOMAIN_PACKAGE_PATH = $this->DNS_PACKAGE_PATH . "/" . $cloud;
	    
	    $domain_backup_dir = $ssh_conn ->sshCmd_try($judge_backup_dir);
	    $domain_package_dir = $ssh_conn ->sshCmd_try($judge_package_dir);

	    if(empty($domain_backup_dir) && empty($domain_package_dir))
	    {
	        return "init false";
	        exit;
	    }
	    else
	    {
	        return "0";
	    }
    }
    
    /*
     * DNS发布初始化--备份现有配置
     */
    private function dns_backup($domain, $cloud)
    {
        $ssh_conn = new OmSsh();
         
        $judge_dns_conf_file = sprintf("ls -1 %s | grep %s",  $this->DNS_DOMAIN_PACKAGE_PATH, $domain);
        $result = $ssh_conn ->sshCmd_try($judge_dns_conf_file);
        if(empty($result))
        {
            return "0";
        }
        else
        {
            $backup_conf_file = sprintf("cp %s/%s %s/%s.%s", $this->DNS_DOMAIN_PACKAGE_PATH, $domain, $this->DNS_DOMAIN_BACKUP_PATH, $domain, $this->TIME);
            $ssh_conn ->sshCmd_try($backup_conf_file);
            //echo "backup end<br>";
            $check_backup_file = sprintf("ls -1 %s |grep %s.%s", $this->DNS_DOMAIN_BACKUP_PATH, $domain, $this->TIME);
            $file = $ssh_conn ->sshCmd_try($check_backup_file);
             
            if(trim($file) !== '')
            {
                return "0";
            }
            else
            {
                return "backup false";
            }
        }
    }
    
    /*
     * DNS发布初始化--生成配置文件
     */
    private function release_dns_makeconf($domain, $cloud)
    {
        $conf_file=fopen($this->DNS_DOMAIN_PACKAGE_PATH . "/" . $domain, "w") or die("Unable to open file!");
        if ($conf_file == 'FALSE')
        {
            return "create conf false";
            exit;
        }
        $txt .= "\$TTL    30\n";
        $txt .= "@       IN      SOA     ns1." . $domain . ".     root.ns." . $domain . ".(\n";
        $txt .= "                        " . $this->DNS_TIME .";\n";
        $txt .= "                        1M         ;\n";
        $txt .= "                        15M        ;\n";
        $txt .= "                        1W         ;\n";
        $txt .= "                        1M )       ;\n";
        $txt .= "                        IN NS      ns." . $domain . ".\n";
        fwrite($conf_file, $txt);
        
        $db_conn = new OmMysql ();
        $row = $db_conn->mysql_query("release_sql.make_dns_conf", array('dns_domain'=> $domain,'dns_cloud'=> $cloud));
        $conf_txt = '';
        while($result = mysql_fetch_array($row))
        {
            //$result_array[] = $result;
            $html .= '<tr id="' . $result ['id'] . '">';
            $conf_txt = $result['dns_record'] . "     IN " . $result['dns_type'] . "     " . $result['dns_value'] . "\n";
            fwrite($conf_file, $conf_txt);
        }
        fclose($conf_file);
        return "0";
    }
    
    /*
     * DNS发布初始化--sync,上传到线上DNS服务器
     */
    private function dns_sync($domain, $cloud)
    {
        /*
         * sync--代码同步到线上服务器
         */
        $ssh_conn = new OmSsh();
        $ip = Yaf_Application::app ()->getConfig ()->application->dns->$cloud->ip;
        
        $sync = sprintf("scp %s/%s %s:/tmp", $this->DNS_DOMAIN_PACKAGE_PATH, $domain, $ip);
        $ssh_conn ->sshCmd_try($sync);
             
        $check_sync = sprintf("ssh %s \"ls -1 /tmp |grep %s\"", $ip, $domain);
        $result_check_sync = $ssh_conn ->sshCmd_try($check_sync);
        if(trim($result_check_sync) == '' )
        {
            return $ip . "sync code false";
            exit;
        }
        
        return "0";
    }
    
    /*
     * DNS发布--修改线上DNS配置
     */
    private function dns_deploy($domain, $cloud)
    {
        $ssh_conn = new OmSsh();
        $ip = Yaf_Application::app ()->getConfig ()->application->dns->$cloud->ip;
        
        $deploy = sprintf("ssh  %s \"sudo cat /tmp/%s > /Data/named/%s.zone\"", $ip, $domain, $cloud);
        //echo $deploy;
        //exit;
        $ssh_conn ->sshCmd_try($deploy);
        
        $size_new = sprintf("ssh %s sudo ls -l /tmp/%s | awk '{print $5}'", $ip, $domain);
        $result_new = $ssh_conn ->sshCmd_try($size_new);
        $size_old = sprintf("ssh %s sudo ls -l /Data/named/%s.zone | awk '{print $5}'", $ip, $cloud);
        $result_old = $ssh_conn ->sshCmd_try($size_old);
        
        if($result_new !== $result_old)
        {
            return $cloud . "dns deploy false";
            exit;
        }
        return "0";
    }
    
    /*
     * DNS发布--BIND reload
     */
    private function dns_reload($domain, $cloud)
    {
        $ssh_conn = new OmSsh();
        $ip = Yaf_Application::app ()->getConfig ()->application->dns->$cloud->ip;
        
        $dns_reload = sprintf("ssh %s \"sudo /Data/local/named-9.9.8-P2/sbin/rndc reload\"", $ip);
        $result = $ssh_conn ->sshCmd_try($dns_reload);
        
        if(trim($result) == "server reload successful")
        {
            return "0";
        }
        else
        {
            return "bind reload false";
        }
    }
    
    /*
     * DNS发布--发布过程
     */
    public function dns_rel_actionAction()
    {
        $domain=$_POST['domain'];
        $cloud=$_POST['cloud'];
        if ($this->release_dns_init($domain, $cloud) !== '0')
        {
            echo "init false";
            exit;
        }
        if ($this->dns_backup($domain, $cloud) !=='0')
        {
            echo "backup conf false";
            exit;
        }
        if ($this->release_dns_makeconf($domain, $cloud) !== '0')
        {
            echo "create conf false";
            exit;
        }
        if ($this->dns_sync($domain, $cloud) !== '0')
        {
            echo $cloud . " sync conf false";
            exit;
        }
        if ($this->dns_deploy($domain, $cloud) !== '0')
        {
            echo $cloud . " dns deploy false";
            exit;
        }
        if ($this->dns_reload($domain, $cloud) !== '0')
        {
            echo $cloud . " bind reload false";
            exit;
        }
        echo "success";
        return false;
    }
    
    /*
     * DNS记录修改--api接口
     */
    public function dns_apiAction()
    {
        $domain=$_POST['domain'];
        $cloud=$_POST['cloud'];
        $record=$_POST['record'];
        $type=$_POST['type'];
        $value=$_POST['value'];
        $operate=$_POST['operate'];
        $key=$_POST['key'];
        $token=yoho9646;
//         echo $token;
//         echo $key;
//         echo $domain;
//         echo $cloud;
//         echo $record;
//         echo $type;
//         echo $value;
//         echo $operate;
//         exit;
        
        if($key != $token)
        {
            echo "Error: No identity";
            exit;
        }
        
        if($operate=="view")
        {
            if(empty($domain) || empty($cloud) || empty($record) || empty($type))
            {
                echo "Missing parameter";
                exit;
            }
            
            if($cloud=="aws")
            {
                $dns_ip = array("172.31.22.217", "172.31.22.29");
            }
            if($cloud=="qcloud")
            {
                $dns_ip = array("10.66.4.17", "10.66.4.18");
            }
            foreach ($dns_ip as $value)
            {
                $ssh_conn = new OmSsh();
                $dns_dig = sprintf("dig @%s %s |grep %s |grep 0 |grep %s", $value, $record . "." . $domain, $type, $record . "." . $domain);
                $result = $ssh_conn ->sshCmd_try($dns_dig);
                echo $value . ":\n" ;
                echo $result;
            }
            
            exit;
        }
        
        if($operate=="update")
        {
            if(empty($domain) || empty($cloud) || empty($record) || empty($type) || empty($value))
            {
                echo "Missing parameter";
                exit;
            }
            
            $db_conn = new OmMysql ();
            $row = $db_conn->mysql_query("release_sql.dns_api_check_record", array('dns_record'=> $record, 
                                                                                   'dns_type'=> $type, 
                                                                                   'dns_domain'=> $domain, 
                                                                                   'dns_cloud'=> $cloud));
            
            $result=mysql_num_rows($row);
            
            if($result !== 1)
            {
                echo "Unable to find the domain:'" . $record . "' resolution";
                exit;
            }
            
            $value_array = explode(',', $value);
            $record_value = $value_array[0];
            $record_type = $value_array[1];
            
            if(empty($record_type))
            {
                $record_type = $type;
            }
            
            $row = $db_conn->mysql_query("release_sql.dns_api_update", array('record_value'=> $record_value,
                                                                                   'record_type'=> $record_type,
                                                                                   'dns_record'=> $record, 
                                                                                   'dns_type'=> $type, 
                                                                                   'dns_domain'=> $domain, 
                                                                                   'dns_cloud'=> $cloud));
            
            //exit;
            if(!$row)
            {
                echo $record . " Update False";
                die('Error: ' . mysql_error());
                exit;
            }
            
            echo "'" . $record . "' Update Success";
            exit;
        }
        
        if($operate=="commit")
        {
            if(empty($domain) || empty($cloud))
            {
                echo "Missing parameter";
                exit;
            }
            
            if ($this->release_dns_init($domain, $cloud) !== '0')
            {
                echo "init false";
                exit;
            }
            if ($this->dns_backup($domain, $cloud) !=='0')
            {
                echo "backup conf false";
                exit;
            }
            if ($this->release_dns_makeconf($domain, $cloud) !== '0')
            {
                echo "create conf false";
                exit;
            }
            if ($this->dns_sync($domain, $cloud) !== '0')
            {
                echo $cloud . " sync conf false";
                exit;
            }
            if ($this->dns_deploy($domain, $cloud) !== '0')
            {
                echo $cloud . " dns deploy false";
                exit;
            }
            if ($this->dns_reload($domain, $cloud) !== '0')
            {
                echo $cloud . " bind reload false";
                exit;
            }
            echo "success";
            exit;
        }
        
        echo "This operation is rejected";
        return false;
    }
    
    /*
     * 发布API
     */
    public function Release_apiAction()
    {
        $project=$_POST['project'];
        $cloud=$_POST['cloud'];
        $operate=$_POST['operate'];
        $key=$_POST['key'];
        $token=yoho9646;
        if($key != $token)
        {
            echo "Error: No identity";
            exit;
        }
        
        $db_conn = new OmMysql ();
        
        if($operate=="release")
        {
//             echo $project;
//             echo $cloud;
//             echo $operate;
//             echo $key;
            
            $project_name = $_POST['project'];
      
            if (empty($project_name))
            {
                echo "project_name not null";
                exit;
            }
            $row = $db_conn->mysql_query("release_sql.release_api_get_project_info", array('project_name'=> $project_name,
                                                                                           'project_region'=> $cloud));
            $result = mysql_fetch_array($row);
//             echo $result['project_name'];
//             echo $result['project_language'];
//             echo $result['project_repository'];
//             echo $result['project_repository_url'];
//             echo $result['project_code_path'];
//             echo $result['project_ip'];
//             echo $result['project_region'];
//             exit;
            
            
            $this->PROJECT_GIT_URL = $result['project_repository_url'];
            $this->PROJECT_ECS_LIST = $result['project_ip'];
            $project_code_path = $result['project_code_path'];
            
            
            
            /*
             * 初始化参数
             */
            if ($this->release_init($project_name) !== '0')
            {
                echo "init false";
                exit();
            }
             
            /*
             * 备份
             */
            if ($this->release_backup($project_name) !== '0')
            {
                echo "backup false";
                exit;
            }
            //echo "backup";
            /*
             * 检查备份文件是否大于10个，如果大于删除多余的备份
             */
            if ($this->release_check_backup($project_name) !== '0')
            {
                echo "check backup false";
                exit;
            }
            //echo "check backup";
            /*
             * git拉取代码
             */
            if ($this->release_update_git($project_name) !== '0')
            {
                echo "git false";
                exit();
            }
            //echo "git";
            /*
             * 打包
             */
            if ($this->release_package($project_name) !== '0')
            {
                echo "package false";
                exit();
            }
            
            /*
             * 上传生产环境服务器
             */
            if ($this->release_sync($project_name) !== '0')
            {
                echo "sync false";
                exit();
            }
             
            /*
             * 覆盖代码
             */
            if ($this->release_deploy($project_name, $project_code_path) !== '0')
            {
                echo "deploy false";
                exit();
            }
            
            /*
             * 修改代码目录权限
             */
            if ($this->release_chown($project_code_path) !== '0')
            {
                echo "chown false";
                exit();
            }
             
            /*
             * 发布记录写入数据库
             */
            $db_conn = new OmMysql ();
            $row = $db_conn->mysql_query ( "release_sql.record_release", array("rel_app"=>$project_name, "rel_publisher"=>"api", "rel_result"=>"release"));
            if(!$row)
            {
                die('Error: ' . mysql_error());
            }
            
            /*
             * 全部成功返回success
             */
            echo "success";
            
            exit;
        }
        
        echo "This operation is rejected";
        return false;
    }
    
    
}
?>
