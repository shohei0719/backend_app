<?php

class IndexController extends ControllerBase
{
	
	public function initialize()
	{
		parent::initialize();
		if(empty(parent::getAuth())){
			$this->response->redirect('/backend_app/signin/');
		}else{
			$this->response->redirect('/backend_app/admin/');
		}
	}
	
	public function indexAction()
	{
		
	}
}
