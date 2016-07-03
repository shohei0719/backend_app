<?php

/*
 * 一番最初の登録時にだけ使う
 * テスト用のDBから管理者を移行すれば必要ない。基本的には使用しないControllerだけど一応残しとく
 */

class SignupController extends ControllerBase
{
	public function initialize()
	{
		parent::initialize();

		$this->admin = new Admins();
	}

  public function indexAction()
  {
  	if ($this->request->isPost() == true) {

  		//Post Params
  		$mail 		= $this->request->getPost('mail');
  		$password = $this->request->getPost('password');
			$name 	  = $this->request->getPost('name');

			//現在時刻取得
			$datetime = date('Y-m-d H:i:s');

			//管理者データ登録
  		$this->admin->mail 				= $mail;
  		$this->admin->password 		= $password;
			$this->admin->name     		= $name;
			$this->admin->permission 	= $this->config->define->frist_permission; //1:全権限
			$this->admin->created_id  = 1;
			$this->admin->created_at  = $datetime;
			$this->admin->updated_id  = 1;
			$this->admin->updated_at  = $datetime;

  		if ($this->admin->save() == false) {
  			//バリデーションエラー内容
  			foreach ($this->admin->getMessages() as $message) {
  				$errorMsg[$message->getField()] = $message->getMessage();
  			}
  			$this->view->errorMsg = $errorMsg;
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
		parent::successRedirect($this->request->isPost(), 'signup');
  }
}
