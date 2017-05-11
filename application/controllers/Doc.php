<?php
class DocController extends Yaf_Controller_Abstract {
	public function init() {
		$this->getView()->setLayout('Consolemain');
	}
	public function indexAction()
	{
		
	}
	
	public function omAction()
	{
	
	}
	
	public function recordAction()
	{
	    echo $_GET['action'];
	    exit;
	    if (empty($_POST['doc_decs']) ||
	        empty($_POST['doc_user']) ||
	        empty($_POST['doc_password']) ||
	        empty($_POST['doc_group']) ||
	        empty($_POST['doc_common']))
	    {
	        echo '<script type="text/javascript">window.onload=function(){alert("所有选项必填。");window.top.location.href="/doc/om";}</script>';
	        exit;
	         
	    }
	    
	    
	    $db_conn = new OmMysql ();
	    $row = $db_conn->mysql_query ( "doc_sql.doc_record", array("description"=>$_POST['doc_decs'], 
	                                                               "username"=>$_POST['doc_user'], 
	                                                               "pass"=>$_POST['doc_password'], 
	                                                               "belongs"=>$_POST['doc_group'],
	                                                               "common"=>$_POST['doc_common']
	                                                              ));
	    if(!$row)
	    {
	        die('Error: ' . mysqli_error($db_conn));
	    }
	    echo '<script type="text/javascript">window.onload=function(){alert("添加成功");window.top.location.href="/doc/om";}</script>';
	    exit;
	
	}
	
	public function getdescAction() {
	    $db_conn = new OmMysql ();
	
	    $row = $db_conn->mysql_query("doc_sql.get_desc", array());
	    header("content-type:application/json");
	    $result_array = array();
	    while($result = mysqli_fetch_row($row))
	    {
	        $result_array[] = $result;
	    }
	    echo json_encode($result_array);
	    return false;
	}
}
?>