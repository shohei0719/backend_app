<?php

class AdminController extends ControllerBase
{

	//private $admin;

	public function initialize()
	{
		parent::initialize();
		/*
		 * ログイン状態をチェック
		 */
	 	$_auth = parent::checkRooting('admin');

		$this->admin = new Admins();
	}

	//一覧
  public function indexAction()
  {

  	$currentPage = ($_GET['page']) ? (int) $_GET['page'] : 1;

		if($_GET['name'] or $_GET['permission'] or $_GET['mail']){ //Searchのときの処理

			$name 		   = ($_GET['name']) ? (string) $_GET['name'] : '';
			$permission  = ($_GET['permission']) ? (int) $_GET['permission'] : 0;
			$mail 		   = ($_GET['mail']) ? (string) substr($_GET['mail'], 0, -1) : ''; //最後に「/」が入るので削除

			//検索結果取得
			$admins = $this->admin->getSearchResult($permission, $name, $mail);

			//Viewに渡す
			$this->view->name 		= $name;
			$this->view->mail 		= $mail;

		} else {

			//検索結果取得
			$admins = $this->admin->getAllResult();

		}

  	$paginator = new Phalcon\Paginator\Adapter\Model(array(
  			"data" 	=> $admins,
  			"limit" => 25,
  			"page" 	=> $currentPage
  	));

  	$page = $paginator->getPaginate();

  	$this->view->setVar("page", $page);
  }

	//編集
	public function editAction(){
		//セッション取得
		$_auth = parent::getAuth();

		//ID取得
		if(!empty($this->request->getQuery('id'))){
			$id = substr($this->request->getQuery('id'), 0, -1);
		}

		//全権限を持たない管理者でも自分自身の管理者情報を変更できるようにする
		parent::checkRooting('admin', $id);

		//検索
		$admin = $this->admin->getAdminInfo($id);
		$created_admin = $this->admin->getAdminInfo($admin->created_id);
		$updated_admin = $this->admin->getAdminInfo($admin->updated_id);

		/*
		 * statusチェック
		 * 0 : 一覧から遷移 ($this->config->define->list)
		 * 1 : 編集から遷移 ($this->config->define->edit)
		 */

		//いつか直す。
		$status = $this->request->getPost('status');
		!empty($status) ? $status : $status = 0;

		switch($status){
			//一覧から遷移
			case $this->config->define->list:

				$this->view->admin = $admin;
				$this->view->created_admin = $created_admin;
				$this->view->updated_admin = $updated_admin;

				break;

			//編集から遷移
			case $this->config->define->edit:
				//Post Params
				$name 	   	= $this->request->getPost('name');
				$mail 	   	= $this->request->getPost('mail');
				$permission = $this->request->getPost('permission');

				$admin->name      	= $name;
				$admin->mail        = $mail;
				$admin->permission 	= $permission;
				$admin->updated_id  = $this->_auth['id'];
				$admin->updated_at  = date('Y-m-d H:i:s');

				if ($admin->save() == false) {

					//バリデーションエラー内容
					foreach ($admin->getMessages() as $message) {
						$errorMsg[$message->getField()] = $message->getMessage();
					}
					$this->logger->info(print_r($errorMsg,1));
					//Viewに渡す
					$this->view->errorMsg  	   = $errorMsg;
					$this->view->admin         = $admin;
					$this->view->created_admin = $created_admin;
					$this->view->updated_admin = $updated_admin;

				} else {

					//自分の情報を更新した場合　＆　セッション更新
					if($id == $this->_auth['id']){
						$admin = $this->admin->getAdminInfo($this->_auth['id']);
						$this->_registerSession($admin);
					}

					$this->dispatcher->forward(
						array(
							'controller' => 'Admin'
							,'action' => 'success'
						)
					);
				}

				break;
		}
	}

	//新規
	public function newAction(){

		if ($this->request->isPost() == true) {

    	//Post Params
    	$name 	    	= $this->request->getPost('name');
			$mail		 			= $this->request->getPost('mail');
    	$password 		= $this->request->getPost('password');
			$re_password 	= $this->request->getPost('re_password');
			$permission 	= $this->request->getPost('permission');

			//現在時刻取得
			$datetime = date('Y-m-d H:i:s');

			//管理者データ登録
    	$this->admin->mail 				= $mail;
    	$this->admin->password 		= $password;
			$this->admin->re_password = $re_password;
			$this->admin->name     		= $name;
			$this->admin->permission 	= $permission;
			$this->admin->created_id  = $this->_auth['id'];
			$this->admin->created_at  = $datetime;
			$this->admin->updated_id  = $this->_auth['id'];
			$this->admin->updated_at  = $datetime;

    	if ($this->admin->save() == false) {
    		//バリデーションエラー内容
    		foreach ($this->admin->getMessages() as $message) {
    			$errorMsg[$message->getField()] = $message->getMessage();
    		}

    		$this->view->errorMsg 		= $errorMsg;
				$this->view->name 	  		= $name;
				$this->view->mail 	  		= $mail;
				$this->view->password 		= $password;
				$this->view->re_password 	= $re_password;
				$this->view->permission 	= $permission;

    	} else {
  			$this->dispatcher->forward(
  				array(
  					 'controller' => 'Admin'
  					,'action'     => 'success'
  				)
  			);
    	}
    }
	}

	//パスワード変更
	public function changeAction(){
		//ID取得
		if(!empty($this->request->getQuery('id'))){
			$id = $this->request->getQuery('id');
		}
		$this->view->id = $id;

		//検索
		$admin = $this->admin->getAdminInfo($id);

		if($this->request->isPost() == true){
			//Post Params
			$password 		 = $this->request->getPost('password');
			$re_password 	 = $this->request->getPost('re_password');

			$admin->password    = $password;
			$admin->re_password = $re_password;
			$admin->updated_id  = $this->_auth['id'];
			$admin->updated_at  = date('Y-m-d H:i:s');

			//DB更新処理
			if ($admin->save() == false) {
				foreach ($admin->getMessages() as $message) {
					$errorMsg[$message->getField()] = $message->getMessage();
				}
				$this->view->errorMsg 	 = $errorMsg;
				$this->view->password 	 = $password;
				$this->view->re_password = $re_password;
			} else {
				$this->dispatcher->forward(
					array(
						'controller' => 'Admin'
						,'action' => 'success'
					)
				);
			}
		}
	}

	//削除
	public function deleteAction(){
		$id = ($_GET['id']) ? (int) $_GET['id'] : 0;

		if(!empty($id) && $id > 0){
			//IDから管理者情報取得
			$admin = $this->admin->getAdminInfo($id);

			//delete_flgのステータスをONにする
			$admin->delete_flg  = $this->config->define->invalid;
			$admin->updated_id  = $this->_auth['id'];
			$admin->updated_at  = date('Y-m-d H:i:s');

			if ($admin->save() == false) {
				foreach ($admin->getMessages() as $message) {
					$errorMsg[$message->getField()] = $message->getMessage();
				}
			} else {
				$this->dispatcher->forward(
    				array(
    					 'controller' => 'Admin'
    					,'action'     => 'success'
    				)
    			);
			}
		}
	}

	//成功時
	public function successAction()
	{
		parent::successRedirect($this->request->isPost(), 'admin');
	}
}
