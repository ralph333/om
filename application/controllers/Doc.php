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
	    echo $_POST['doc_decs'];
	    echo $_POST['doc_user'];
	    echo $_POST['doc_password'];
	    echo $_POST['doc_group'];
	    if (empty($_POST['doc_decs']) ||
	        empty($_POST['doc_user']) ||
	        empty($_POST['doc_password']) ||
	        empty($_POST['doc_group']))
	    {
	        echo '<script type="text/javascript">window.onload=function(){alert("所有选项必填。");window.top.location.href="/doc/om";}</script>';
	        exit;
	         
	    }
	    
	    
	    $db_conn = new OmMysql ();
	    $row = $db_conn->mysql_query ( "doc_sql.doc_record", array("description"=>$_POST['doc_decs'], 
	                                                               "username"=>$_POST['doc_user'], 
	                                                               "pass"=>$_POST['doc_password'], 
	                                                               "belongs"=>$_POST['doc_group']
	                                                              ));
	    if(!$row)
	    {
	        die('Error: ' . mysqli_error($db_conn));
	    }
	    
	
	}
}
?>