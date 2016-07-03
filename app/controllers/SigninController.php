<?php

class SigninController extends ControllerBase
{
	public function initialize()
	{
		parent::initialize();
	}

  public function indexAction()
  {

  	if($this->request->isPost() == true){

  		//Post Params
  		$mail		= $this->request->getPost('mail');
  		$password	= $this->request->getPost('password');

			//select * from Admins where mail = $mail;
  		$admin = Admins::findFirst(array(
				"(mail = :mail:)",
      	'bind' => array('mail' => $mail)
      ));

			//メールアドレス hashチェック
  		if(!empty($admin->mail)){
  			if ($this->security->checkHash($password , $admin->password)) {
  				$this->_registerSession($admin);
  				$this->response->redirect('/backend_app/');
		    }
  		}

  		$this->view->errorMsg = $this->config->validates_signin->check_mail_password;
  	}
  }
	
}
