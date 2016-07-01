<?php

class SigninController extends ControllerBase
{
	/*
	 * session
	 */
	/*
	private function _registerSession(Users $user){
		$this->session->set('auth', array(
			'id' => $user->id,
			'email' => $user->email,
			'name' => $user->name
		));
	}
	*/
	public function initialize()
	{
		parent::initialize();
	}
	
    public function indexAction()
    {
    	
    	if($this->request->isPost() == true){
    		//$user = new Users();
    		
    		//Post Params
    		$mail		= $this->request->getPost('mail');
    		$password	= $this->request->getPost('password');
    		
    		//echo $mail;
    		
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
    		
    		$this->view->errorMsg = 'メールアドレスもしくはパスワードが間違っています。';
    	}
    }
    
}