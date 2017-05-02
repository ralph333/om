<?php
class ConsoleController extends Yaf_Controller_Abstract {
	public function init()
	{
		$this->getView()->setLayout('Consolemain');
	}
	
	public function indexAction()
	{
		$this->getView()->assign("content");
	}
}
?>