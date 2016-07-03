<?php

class SignoutController extends ControllerBase
{

	public function initialize()
	{
		//parent::initialize();
	}

  public function indexAction()
  {
  	$request = new \Phalcon\Http\Request();
  	parent::_removeSession();
  	$this->response->redirect($request->getHTTPReferer());
  }
}
