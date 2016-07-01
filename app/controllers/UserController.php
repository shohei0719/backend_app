<?php

class UserController extends ControllerBase
{

	public function initialize()
	{
		parent::initialize();
		//ログイン状態をチェック
		if(!empty(parent::getAuth())){
			$_auth = parent::getAuth();
			//管理者情報編集権限がない場合は/carrier/にリダイレクト
			if($_auth['permission'] > 2){
				if(!preg_match('/edit/', $_SERVER['REQUEST_URI']) and !preg_match('/change/', $_SERVER['REQUEST_URI'])){
					$this->response->redirect('/backend_app/terminal/');
				}
			}
		}else{
			$this->response->redirect('/backend_app/signin/');
		}

	}

	//一覧
  public function indexAction()
  {

  	$currentPage = ($_GET['page']) ? (int) $_GET['page'] : 1;

		if($_GET['name'] or $_GET['mail']){ //Searchのときの処理

			$name 		 = ($_GET['name']) ? (string) $_GET['name'] : '';
			$mail 		 = ($_GET['mail']) ? (string) substr($_GET['mail'], 0, -1) : ''; //最後に「/」が入るので削除
			$criteria = Users::query();

			if(!empty($name)){
				$criteria->andwhere('name LIKE :name:', ['name' => '%' . $name . '%']);
			}
			if(!empty($mail)){
				$criteria->andwhere('mail LIKE :mail:', ['mail' => '%' . $mail . '%']);
			}
			$criteria->andwhere('delete_flg = :delete_flg:', ['delete_flg' => 0]);
			$users = $criteria->execute();

			//Viewに渡す
			$this->view->name 		= $name;
			$this->view->mail 		= $mail;

		} else {

			$criteria = Users::query();
			$criteria->andwhere('delete_flg = :delete_flg:', ['delete_flg' => 0]);
			$users = $criteria->execute();

		}

  	$paginator = new Phalcon\Paginator\Adapter\Model(array(
  			"data" => $users,
  			"limit" => 25,
  			"page" => $currentPage
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

		//検索
		$user = Users::findFirst(array(
			"(id = :id:)",
			'bind' => array('id' => $id)
		));

		//作成者情報を取得
		if($user->created_user_type == 1){
			$created_user = Admins::findFirst(array(
				"(id = :id:)",
				'bind' => array('id' => $user->created_id)
			));
		} else {
			$created_user = Users::findFirst(array(
				"(id = :id:)",
				'bind' => array('id' => $user->created_id)
			));
		}

		//更新者情報を取得
		if($user->updated_user_type == 1){
			$updated_user = Admins::findFirst(array(
				"(id = :id:)",
				'bind' => array('id' => $user->updated_id)
			));
		} else {
			$updated_user = Users::findFirst(array(
				"(id = :id:)",
				'bind' => array('id' => $user->updated_id)
			));
		}

		/*
		 * statusチェック
		 * 0 : 一覧から遷移
		 * 1 : 編集から遷移
		 */
		switch($this->request->getPost('status')){
			//一覧から遷移
			case 0:

				$this->view->user = $user;
				$this->view->created_user = $created_user;
				$this->view->updated_user = $updated_user;

				break;

			//編集から遷移
			case 1:
				//Post Params
				$name 	   	= $this->request->getPost('name');
				$mail 	   	= $this->request->getPost('mail');

				$user->name      	 = $name;
				$user->mail        = $mail;
				$user->updated_user_type = 1; //1:管理者 2:ユーザ
				$user->updated_id  = $this->_auth['id'];
				$user->updated_at  = date('Y-m-d H:i:s');

				if ($user->save() == false) {

					//バリデーションエラー内容
					foreach ($user->getMessages() as $message) {
						$errorMsg[$message->getField()] = $message->getMessage();
					}
					$this->logger->info(print_r($errorMsg,1));
					//Viewに渡す
					$this->view->errorMsg  	   = $errorMsg;
					$this->view->user          = $user;
					$this->view->created_user = $created_user;
					$this->view->updated_user = $updated_user;

				} else {

					//自分の情報を更新した場合　＆　セッション更新
					if($id == $this->_auth['id']){
						$user = Users::findFirst(array(
							"(id = :id:)",
							'bind' => array('id' => $this->_auth['id'])
						));
						$this->_registerSession($user);
					}

					$this->dispatcher->forward(
						array(
							'controller' => 'User'
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
    		$user = new Users();

    		//Post Params
    		$name 	    	= $this->request->getPost('name');
			  $mail		 			= $this->request->getPost('mail');
    		$password 		= $this->request->getPost('password');
				$re_password 	= $this->request->getPost('re_password');

				//現在時刻取得
				$datetime = date('Y-m-d H:i:s');

				//管理者データ登録
    		$user->mail 		   = $mail;
    		$user->password 	 = $password;
				$user->re_password = $re_password;
				$user->name     	 = $name;
				$user->created_user_type = 1; //1:管理者 2:ユーザ
				$user->created_id  = $this->_auth['id'];
				$user->created_at  = $datetime;
				$user->updated_user_type = 1; //1:管理者 2:ユーザ
				$user->updated_id  = $this->_auth['id'];
				$user->updated_at  = $datetime;

    		if ($user->save() == false) {
    			//バリデーションエラー内容
    			foreach ($user->getMessages() as $message) {
    				$errorMsg[$message->getField()] = $message->getMessage();
    			}

    			$this->view->errorMsg 		= $errorMsg;
					$this->view->name 	  		= $name;
					$this->view->mail 	  		= $mail;
					$this->view->password 		= $password;
					$this->view->re_password 	= $re_password;

    		} else {
    			$this->dispatcher->forward(
    				array(
    					 'controller' => 'User'
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
		$user = Users::findFirst(array(
			"(id = :id:)",
			'bind' => array('id' => $id)
		));

		if($this->request->isPost() == true){
			//Post Params
			$password 		 = $this->request->getPost('password');
			$re_password 	 = $this->request->getPost('re_password');

			$user->password    = $password;
			$user->re_password = $re_password;
			$user->updated_user_type = 1; //1:管理者 2:ユーザ
			$user->updated_id  = $this->_auth['id'];
			$user->updated_at  = date('Y-m-d H:i:s');

			//DB更新処理
			if ($user->save() == false) {
				foreach ($user->getMessages() as $message) {
					$errorMsg[$message->getField()] = $message->getMessage();
				}
				$this->view->errorMsg 	 = $errorMsg;
				$this->view->password 	 = $password;
				$this->view->re_password = $re_password;
			} else {
				$this->dispatcher->forward(
					array(
						'controller' => 'User'
						,'action' => 'success'
					)
				);
			}
		}
	}

	public function deleteAction(){
		$id = ($_GET['id']) ? (int) $_GET['id'] : 0;

		if(!empty($id) && $id > 0){
			//IDから管理者情報取得
			$admin = Users::findFirst(array(
				"(id = :id:)",
				'bind' => array('id' => $id)
			));
			//delete_flgのステータスをONにする
			$admin->delete_flg = 1;
			$user->updated_user_type = 1; //1:管理者 2:ユーザ
			$admin->updated_id  = $this->_auth['id'];
			$admin->updated_at  = date('Y-m-d H:i:s');

			if ($user->save() == false) {
				foreach ($user->getMessages() as $message) {
					$errorMsg[$message->getField()] = $message->getMessage();
				}
$this->logger->info(print_r($errorMsg,1));
			} else {
				$this->dispatcher->forward(
    				array(
    					 'controller' => 'User'
    					,'action'     => 'success'
    				)
    			);
			}
		}
	}

	//成功時
	public function successAction(){
		//編集画面から遷移時のみ表示
    	if($this->request->isPost() == false){
    		$this->response->redirect('/backend_app/');
    	}
	}
}
