<?php

class OrganizationController extends ControllerBase
{

	public function initialize()
	{
		parent::initialize();
		//ログイン状態をチェック
		if(!empty(parent::getAuth())){
			$_auth = parent::getAuth();
		}else{
			$this->response->redirect('/backend_app/signin/');
		}
	}

	//一覧
    public function indexAction()
    {

    	$currentPage = ($_GET['page']) ? (int) $_GET['page'] : 1;

		if($_GET['name']){ //Searchのときの処理

			$name = ($_GET['name']) ? (string) substr($_GET['name'], 0, -1) : ''; //最後に「/」が入るので削除

			$criteria = Organizations::query();

			if(!empty($name)){
//$this->logger->info($name);
				$criteria->andwhere('name LIKE :name:', ['name' => '%' . $name . '%']);
			}

			$criteria->andwhere('delete_flg = :delete_flg:', ['delete_flg' => 0]);
			$organization = $criteria->execute();

			//Viewに渡す
			$this->view->name = $name;

		} else {
			$criteria = Organizations::query();
			$criteria->andwhere('delete_flg = :delete_flg:', ['delete_flg' => 0]);
			$organizations = $criteria->execute();
		}


    	$paginator = new Phalcon\Paginator\Adapter\Model(array(
    			"data" => $organizations,
    			"limit" => 25,
    			"page" => $currentPage
    	));

    	$page = $paginator->getPaginate();
    	$this->view->setVar("page", $page);
    }

	//編集
	public function editAction(){

		//ID取得
		if(!empty($this->request->getQuery('id'))){
			$id = $this->request->getQuery('id');
		}

		//検索
		$organization = Organizations::findFirst(array(
			"(id = :id:)",
			'bind' => array('id' => $id)
		));

		//作成者情報を取得
		$created_admin = Admins::findFirst(array(
			"(id = :id:)",
			'bind' => array('id' => $organization->created_id)
		));

		//更新者情報を取得
		$updated_admin = Admins::findFirst(array(
			"(id = :id:)",
			'bind' => array('id' => $organization->updated_id)
		));

		/*
		 * statusチェック
		 * 0 : 一覧から遷移
		 * 1 : 編集から遷移
		 */
		switch($this->request->getPOrganizationt('status')){
			//一覧から遷移
			case 0:

				$this->view->maker = $organization;
				$this->view->created_admin = $created_admin;
				$this->view->updated_admin = $updated_admin;

				break;

			//編集から遷移
			case 1:
				//Post Params
				$name = $this->request->getPost('name');

				$organization->name        = $name;
				$organization->updated_id  = $this->_auth['id'];
				$organization->updated_at  = date('Y-m-d H:i:s');

				if ($organization->save() == false) {

					//バリデーションエラー内容
					foreach ($admin->getMessages() as $message) {
						$errorMsg[$message->getField()] = $message->getMessage();
					}
					$this->logger->info(print_r($errorMsg,1));
					//Viewに渡す
					$this->view->errorMsg  	   = $errorMsg;
					$this->view->maker         = $organization;
					$this->view->created_admin = $created_admin;
					$this->view->updated_admin = $updated_admin;

				} else {

					$this->dispatcher->forward(
						array(
							'controller' => 'Maker'
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
    		$organization = new Organizations();

    		//Post Params
    		$name 	    	= $this->request->getPost('name');

			//現在時刻取得
			$datetime = date('Y-m-d H:i:s');

			//キャリアデータ登録
			$organization->name     	= $name;
			$organization->created_id  = $this->_auth['id'];
			$organization->created_at  = $datetime;
			$organization->updated_id  = $this->_auth['id'];
			$organization->updated_at  = $datetime;

    		if ($organization->save() == false) {
    			//バリデーションエラー内容
    			foreach ($organization->getMessages() as $message) {
    				$errorMsg[$message->getField()] = $message->getMessage();
    			}

    			$this->view->errorMsg 		= $errorMsg;
				$this->view->name 	  		= $name;

    		} else {
    			$this->dispatcher->forward(
    				array(
    					 'controller' => 'Organization'
    					,'action'     => 'success'
    				)
    			);
    		}
    	}

	}

	public function deleteAction(){
		$id = ($_GET['id']) ? (int) $_GET['id'] : 0;

		if(!empty($id) && $id > 0){
			//IDから管理者情報取得
			$organization = Organizations::findFirst(array(
				"(id = :id:)",
				'bind' => array('id' => $id)
			));
			//delete_flgのステータスをONにする
			$organization->delete_flg = 1;
			$organization->updated_id  = $this->_auth['id'];
			$organization->updated_at  = date('Y-m-d H:i:s');

			if ($organization->save() == false) {
				foreach ($organization->getMessages() as $message) {
					$errorMsg[$message->getField()] = $message->getMessage();
				}
$this->logger->info(print_r($errorMsg,1));
			} else {
				$this->dispatcher->forward(
    				array(
    					 'controller' => 'Organization'
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
