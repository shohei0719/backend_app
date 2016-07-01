<?php

class SignoutController extends ControllerBase
{
	/*
	 * session
	 */
	private function _removeSession(){
		$this->session->destroy();
	}
	
	public function initialize()
	{
		//parent::initialize();
	}
	
    public function indexAction()
    {
    	$request = new \Phalcon\Http\Request();
    	$this->_removeSession();
    	$this->response->redirect($request->getHTTPReferer());
    }
    
}