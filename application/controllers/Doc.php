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
	
	}
}
?>