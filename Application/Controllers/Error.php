<?php

namespace Application\Controllers;

class Error extends \Library\Controller\Controller{


	public function __construct(){
		parent::__construct();
		$this->setLayout("signin");
	}


	public function indexAction(){
		header("HTTP/1.0 404 Not Found");
	}
	public function enconstructionAction(){
		
	}
}