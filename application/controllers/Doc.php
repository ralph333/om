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