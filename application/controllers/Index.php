<?php
class IndexController extends Yaf_Controller_Abstract {
	public function indexAction() {
       //$this->getView()->assign("content", "FUCK whb");
       header("location: /console");
   }
}
?>