<?php

class UserController extends ControllerBase
{

	public function initialize()
	{
		parent::initialize();
		/*
		 * ログイン状態をチェック
		 */
	 	$_auth = parent::checkRooting('user');

		$this->user = new Users();
		$this->admin = new Admins();
	}

	//一覧
  public function indexAction()
  {

  	$currentPage = ($_GET['page']) ? (int) $_GET['page'] : 1;

		if($_GET['name'] or $_GET['mail']){ //Searchのときの処理

			$name = ($_GET['name']) ? (string) $_GET['name'] : '';
			$mail = ($_GET['mail']) ? (string) substr($_GET['mail'], 0, -1) : ''; //最後に「/」が入るので削除

			$users = $this->user->getSearchResult($name, $mail);

			//Viewに渡す
			$this->view->name 		= $name;
			$this->view->mail 		= $mail;

		} else {

			$users = $this->user->getAllResult();

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
		$user = $this->user->getUserInfo($id);

		//作成者情報を取得
		if($user->created_user_type == 1){
			$created_user = $this->admin->getAdminInfo($user->created_id);
		} else {
			$created_user = $this->user->getUserInfo($user->created_id);
		}

		//更新者情報を取得
		if($user->updated_user_type == 1){
			$updated_user = $this->admin->getAdminInfo($user->updated_id);
		} else {
			$updated_user = $this->user->getUserInfo($user->updated_id);
		}

		/*
		 * statusチェック
		 * 1 : 編集から遷移 ($this->config->define->edit)
		 */
		switch($this->request->getPost('status')){

			//編集から遷移
			case $this->config->define->edit:
				//Post Params
				$name 	   	= $this->request->getPost('name');
				$mail 	   	= $this->request->getPost('mail');

				$user->name      	 = $name;
				$user->mail        = $mail;
				$user->updated_user_type = $this->config->define->admin; //1:管理者 2:ユーザ
				$user->updated_id  = $this->_auth['id'];
				$user->updated_at  = date('Y-m-d H:i:s');

				if ($user->save() == false) {

					//バリデーションエラー内容
					foreach ($user->getMessages() as $message) {
						$errorMsg[$message->getField()] = $message->getMessage();
					}

					//Viewに渡す
					$this->view->errorMsg  	  = $errorMsg;
					$this->view->user         = $user;
					$this->view->created_user = $created_user;
					$this->view->updated_user = $updated_user;

				} else {

					//自分の情報を更新した場合　＆　セッション更新
					if($id == $this->_auth['id']){
						$user = $this->user->getUserInfo($this->_auth['id']);
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

			//一覧から遷移
			default:

				$this->view->user = $user;
				$this->view->created_user = $created_user;
				$this->view->updated_user = $updated_user;

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

				//現在時刻取得
				$datetime = date('Y-m-d H:i:s');

				//管理者データ登録
    		$this->user->mail 		   = $mail;
    		$this->user->password 	 = $password;
				$this->user->re_password = $re_password;
				$this->user->name     	 = $name;
				$this->user->created_user_type = $this->config->define->admin; //1:管理者 2:ユーザ
				$this->user->created_id  = $this->_auth['id'];
				$this->user->created_at  = $datetime;
				$this->user->updated_user_type = $this->config->define->admin; //1:管理者 2:ユーザ
				$this->user->updated_id  = $this->_auth['id'];
				$this->user->updated_at  = $datetime;

    		if ($this->user->save() == false) {
    			//バリデーションエラー内容
    			foreach ($this->user->getMessages() as $message) {
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
		$user = $this->user->getUserInfo($id);

		if($this->request->isPost() == true){
			//Post Params
			$password 		 = $this->request->getPost('password');
			$re_password 	 = $this->request->getPost('re_password');

			$user->password    = $password;
			$user->re_password = $re_password;
			$user->updated_user_type = $this->config->define->admin; //1:管理者 2:ユーザ
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
			$user = $this->carrier->getUserInfo($id);

			//delete_flgのステータスをONにする
			$user->delete_flg = $this->config->define->invalid;
			$user->updated_user_type = $this->config->define->admin; //1:管理者 2:ユーザ
			$user->updated_id  = $this->_auth['id'];
			$user->updated_at  = date('Y-m-d H:i:s');

			if ($user->save() == false) {
				foreach ($user->getMessages() as $message) {
					$errorMsg[$message->getField()] = $message->getMessage();
				}
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
	public function successAction()
	{
		parent::successRedirect($this->request->isPost(), 'user');
	}
}
