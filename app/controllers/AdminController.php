<?php

class AdminController extends ControllerBase
{

	//private $admin;

	public function initialize()
	{
		parent::initialize();
		//ログイン状態をチェック
		if(!empty(parent::getAuth())){
			$_auth = parent::getAuth();
			//管理者情報編集権限がない場合は/user/にリダイレクト
			if($_auth['permission'] != 1){
				if(!preg_match('/edit/', $_SERVER['REQUEST_URI']) and !preg_match('/change/', $_SERVER['REQUEST_URI'])){
					$this->response->redirect('/backend_app/carrier/');
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

			if($_GET['name'] or $_GET['permission'] or $_GET['mail']){ //Searchのときの処理

				$name 		 = ($_GET['name']) ? (string) $_GET['name'] : '';
				$permission  = ($_GET['permission']) ? (int) $_GET['permission'] : 0;
				$mail 		 = ($_GET['mail']) ? (string) substr($_GET['mail'], 0, -1) : ''; //最後に「/」が入るので削除
				$criteria = Admins::query();

				if(!empty($permission)){
					$criteria->andwhere('permission = :permission:', ['permission' => $permission]);
				}
				if(!empty($name)){
					$criteria->andwhere('name LIKE :name:', ['name' => '%' . $name . '%']);
				}
				if(!empty($mail)){
					$criteria->andwhere('mail LIKE :mail:', ['mail' => '%' . $mail . '%']);
				}
				$criteria->andwhere('delete_flg = :delete_flg:', ['delete_flg' => 0]);
				$admins = $criteria->execute();

				//Viewに渡す
				$this->view->name 		= $name;
				$this->view->mail 		= $mail;

			} else {
				//$admins = Admins::find();
				$criteria = Admins::query();
				$criteria->andwhere('delete_flg = :delete_flg:', ['delete_flg' => 0]);
				$admins = $criteria->execute();
			}


    	$paginator = new Phalcon\Paginator\Adapter\Model(array(
    			"data" => $admins,
    			"limit" => 25,
    			"page" => $currentPage
    	));

    	//$this->view->paginator = $pginator
    	$page = $paginator->getPaginate();

    	//$this->logger->info(print_r($page,1));
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

		//permissonが全権限でない場合
		if($_auth['permission'] != 1){
			//getパラメータidとと自分のidが同じでない場合は戻す
			if($id != (string) $_auth['id']){
				$this->response->redirect('/backend_app/user/');
			}
		}

		//検索
		$admin = Admins::findFirst(array(
			"(id = :id:)",
			'bind' => array('id' => $id)
		));

		//作成者情報を取得
		$created_admin = Admins::findFirst(array(
			"(id = :id:)",
			'bind' => array('id' => $admin->created_id)
		));

		//更新者情報を取得
		$updated_admin = Admins::findFirst(array(
			"(id = :id:)",
			'bind' => array('id' => $admin->updated_id)
		));

		/*
		 * statusチェック
		 * 0 : 一覧から遷移
		 * 1 : 編集から遷移
		 */
		switch($this->request->getPost('status')){
			//一覧から遷移
			case 0:

				$this->view->admin = $admin;
				$this->view->created_admin = $created_admin;
				$this->view->updated_admin = $updated_admin;

				break;

			//編集から遷移
			case 1:
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
						$admin = Admins::findFirst(array(
							"(id = :id:)",
							'bind' => array('id' => $this->_auth['id'])
						));
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
    		$admin = new Admins();

    		//Post Params
    		$name 	    	= $this->request->getPost('name');
			$mail 			= $this->request->getPost('mail');
    		$password 		= $this->request->getPost('password');
			$re_password 	= $this->request->getPost('re_password');
			$permission 	= $this->request->getPost('permission');

			//現在時刻取得
			$datetime = date('Y-m-d H:i:s');

			//管理者データ登録
    		$admin->mail 		= $mail;
    		$admin->password 	= $password;
			$admin->re_password = $re_password;
			$admin->name     	= $name;
			$admin->permission 	= $permission;
			$admin->created_id  = $this->_auth['id'];
			$admin->created_at  = $datetime;
			$admin->updated_id  = $this->_auth['id'];
			$admin->updated_at  = $datetime;

    		if ($admin->save() == false) {
    			//バリデーションエラー内容
    			foreach ($admin->getMessages() as $message) {
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
		$admin = Admins::findFirst(array(
			"(id = :id:)",
			'bind' => array('id' => $id)
		));

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

	public function deleteAction(){
		$id = ($_GET['id']) ? (int) $_GET['id'] : 0;

		if(!empty($id) && $id > 0){
			//IDから管理者情報取得
			$admin = Admins::findFirst(array(
				"(id = :id:)",
				'bind' => array('id' => $id)
			));
			//delete_flgのステータスをONにする
			$admin->delete_flg = 1;
			$admin->updated_id  = $this->_auth['id'];
			$admin->updated_at  = date('Y-m-d H:i:s');

			if ($admin->save() == false) {
				foreach ($admin->getMessages() as $message) {
					$errorMsg[$message->getField()] = $message->getMessage();
				}
$this->logger->info(print_r($errorMsg,1));
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
	public function successAction(){
		//編集画面から遷移時のみ表示
    	if($this->request->isPost() == false){
    		$this->response->redirect('/backend_app/');
    	}
	}
}
