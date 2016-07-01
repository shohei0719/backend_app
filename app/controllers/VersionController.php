<?php

class VersionController extends ControllerBase
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

			$criteria = Oss::query();
			$criteria->andwhere('delete_flg = :delete_flg:', ['delete_flg' => 0]);
			$oss = $criteria->execute();

			$this->view->oss = $oss;

		if($_GET['related_os'] or $_GET['name']){ //Searchのときの処理

			$related_os = ($_GET['related_os']) ? $_GET['related_os'] : 0;
			$name = ($_GET['name']) ? (string) substr($_GET['name'], 0, -1) : ''; //最後に「/」が入るので削除

			$criteria = Versions::query();
			$criteria->columns('Versions.id, o.name as os_name, Versions.name');
			$criteria->leftJoin('Oss', 'o.id = Versions.related_os', 'o');

			//related_osがPostされているとき
			if(!empty($related_os)){
				$criteria->andwhere('Versions.related_os = :related_os:', ['related_os' => $related_os]);
			}

			//nameがPostされているとき
			if(!empty($name)){
//$this->logger->info($name);
				$criteria->andwhere('Versions.name LIKE :name:', ['name' => '%' . $name . '%']);
			}

			$criteria->andwhere('Versions.delete_flg = :delete_flg:', ['delete_flg' => 0]);
			$criteria->andwhere('o.delete_flg = :delete_flg:', ['delete_flg' => 0]);
			$versions = $criteria->execute();

			//Viewに渡す
			$this->view->related_os = $related_os;
			$this->view->name = $name;

		} else {
			$criteria = Versions::query();
			$criteria->columns('Versions.id, o.name as os_name, Versions.name');
			$criteria->leftJoin('Oss', 'o.id = Versions.related_os', 'o');
			$criteria->andwhere('Versions.delete_flg = :delete_flg:', ['delete_flg' => 0]);
			$criteria->andwhere('o.delete_flg = :delete_flg:', ['delete_flg' => 0]);

			$versions = $criteria->execute();
//$this->logger->info(print_r($versions,1));
		}


    	$paginator = new Phalcon\Paginator\Adapter\Model(array(
    			"data" => $versions,
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
		$version = Versions::findFirst(array(
			"(id = :id:)",
			'bind' => array('id' => $id)
		));

		//作成者情報を取得
		$created_admin = Admins::findFirst(array(
			"(id = :id:)",
			'bind' => array('id' => $version->created_id)
		));

		//更新者情報を取得
		$updated_admin = Admins::findFirst(array(
			"(id = :id:)",
			'bind' => array('id' => $version->updated_id)
		));

		$criteria = Oss::query();
		$criteria->andwhere('delete_flg = :delete_flg:', ['delete_flg' => 0]);
		$oss = $criteria->execute();

		$this->view->oss = $oss;

		/*
		 * statusチェック
		 * 0 : 一覧から遷移
		 * 1 : 編集から遷移
		 */
		switch($this->request->getPost('status')){
			//一覧から遷移
			case 0:

				$this->view->version = $version;
				$this->view->created_admin = $created_admin;
				$this->view->updated_admin = $updated_admin;

				break;

			//編集から遷移
			case 1:
				//Post Params
				$related_os = $this->request->getPost('related_os');
				$name = $this->request->getPost('name');
				$version->related_os  = $related_os;
				$version->name        = $name;
				$version->updated_id  = $this->_auth['id'];
				$version->updated_at  = date('Y-m-d H:i:s');

				if ($version->save() == false) {

					//バリデーションエラー内容
					foreach ($version->getMessages() as $message) {
						$errorMsg[$message->getField()] = $message->getMessage();
					}
					$this->logger->info(print_r($errorMsg,1));
					//Viewに渡す
					$this->view->errorMsg  	   = $errorMsg;
					$this->view->version       = $version;
					$this->view->created_admin = $created_admin;
					$this->view->updated_admin = $updated_admin;

				} else {

					$this->dispatcher->forward(
						array(
							'controller' => 'Version'
							,'action' => 'success'
						)
					);
				}

				break;
		}
	}

	//新規
	public function newAction(){

		$criteria = Oss::query();
		$criteria->andwhere('delete_flg = :delete_flg:', ['delete_flg' => 0]);
		$oss = $criteria->execute();

		$this->view->oss = $oss;
//$this->logger->info($oss);
		if ($this->request->isPost() == true) {
    	$version = new Versions();

    	//Post Params
    	$name 	   	 	= $this->request->getPost('name');
		 	$related_os 	= $this->request->getPost('related_os');

//$this->logger->info($related_os);

			//現在時刻取得
			$datetime = date('Y-m-d H:i:s');

			//キャリアデータ登録
			$version->name     	  = $name;
			$version->related_os  = $related_os;
			$version->created_id  = $this->_auth['id'];
			$version->created_at  = $datetime;
			$version->updated_id  = $this->_auth['id'];
			$version->updated_at  = $datetime;

    		if ($version->save() == false) {
    			//バリデーションエラー内容
    			foreach ($version->getMessages() as $message) {
    				$errorMsg[$message->getField()] = $message->getMessage();
    			}

    			$this->view->errorMsg 		= $errorMsg;
				$this->view->name 	  		= $name;
				$this->view->related_os 	= $related_os;

    		} else {
    			$this->dispatcher->forward(
    				array(
    					 'controller' => 'Version'
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
			$version = Versions::findFirst(array(
				"(id = :id:)",
				'bind' => array('id' => $id)
			));
			//delete_flgのステータスをONにする
			$version->delete_flg = 1;
			$version->updated_id  = $this->_auth['id'];
			$version->updated_at  = date('Y-m-d H:i:s');

			if ($version->save() == false) {
				foreach ($version->getMessages() as $message) {
					$errorMsg[$message->getField()] = $message->getMessage();
				}
//$this->logger->info(print_r($errorMsg,1));
			} else {
				$this->dispatcher->forward(
    				array(
    					 'controller' => 'Version'
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
