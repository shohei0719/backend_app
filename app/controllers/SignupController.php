<?php

class SignupController extends ControllerBase
{
	public function initialize()
	{
		parent::initialize();
	}
	
    public function indexAction()
    {	
    	if ($this->request->isPost() == true) {
    		$admin = new Admins();
    		 
    		//Post Params
    		$mail 		= $this->request->getPost('mail');
    		$password 	= $this->request->getPost('password');
			$name 	    = $this->request->getPost('name');
    		 
			//現在時刻取得
			$datetime = date('Y-m-d H:i:s');
			
			//管理者データ登録
    		$admin->mail 		= $mail;
    		//$user->password = (empty($password)) ? $password : $this->security->hash($password);
    		$admin->password 	= $password;
			$admin->name     	= $name;
			$admin->permission 	= 1; //0:全権限
			$admin->created_id  = 1; //admin
			$admin->created_at  = $datetime;
			$admin->updated_id  = 1; //admin
			$admin->updated_at  = $datetime;
    		 
    		if ($admin->save() == false) {
    			//バリデーションエラー内容
    			foreach ($admin->getMessages() as $message) {
    				$errorMsg[$message->getField()] = $message->getMessage();
    			}
    			$this->view->errorMsg = $errorMsg;
				$this->logger->info(print_r($errorMsg,1));
    		} else {
    			$this->dispatcher->forward(
    				array(
    					 'controller' => 'Signup'
    					,'action'     => 'success'
    				)
    			);
    		}
    	}
    }
    
    //登録成功時
    public function successAction()
    {
    	//新規登録画面から遷移時のみ表示
    	if($this->request->isPost() == false){
    		$this->response->redirect();
    	}
    }
    
}