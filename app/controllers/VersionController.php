<?php

class VersionController extends ControllerBase
{

	public function initialize()
	{
		parent::initialize();
		/*
		 * ログイン状態をチェック
		 */
	 	$_auth = parent::checkRooting('version');

		$this->version = new Versions();
		$this->os			 = new Oss();
		$this->admin   = new Admins();
	}

	//一覧
  public function indexAction()
  {

  	$currentPage = ($_GET['page']) ? (int) $_GET['page'] : 1;

		$oss = $this->os->getAllResult();
		$this->view->oss = $oss;

		if($_GET['related_os'] or $_GET['name']){ //Searchのときの処理

			$related_os = ($_GET['related_os']) ? $_GET['related_os'] : 0;
			$name 			= ($_GET['name']) ? (string) substr($_GET['name'], 0, -1) : ''; //最後に「/」が入るので削除

			$versions = $this->version->getSearchResult($related_os, $name);

			//Viewに渡す
			$this->view->related_os = $related_os;
			$this->view->name = $name;

		} else {

			$versions = $this->version->getAllResult();

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
		$version = $this->version->getVersionInfo($id);
		$created_admin = $this->admin->getAdminInfo($version->created_id);
		$updated_admin = $this->admin->getAdminInfo($version->updated_id);

		$oss = $this->os->getAllResult();
		$this->view->oss = $oss;

		/*
		 * statusチェック
		 * 1 : 編集から遷移 ($this->config->define->edit)
		 */
		switch($this->request->getPost('status')){

			//編集から遷移
			case $this->config->define->edit:
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

			//一覧から遷移
			default:

				$this->view->version = $version;
				$this->view->created_admin = $created_admin;
				$this->view->updated_admin = $updated_admin;

				break;
		}
	}

	//新規
	public function newAction(){

		$oss = $this->os->getAllResult();
		$this->view->oss = $oss;

		if ($this->request->isPost() == true) {

    	//Post Params
    	$name 	   	 	= $this->request->getPost('name');
		 	$related_os 	= $this->request->getPost('related_os');

			//現在時刻取得
			$datetime = date('Y-m-d H:i:s');

			//キャリアデータ登録
			$this->version->name     	  = $name;
			$this->version->related_os  = $related_os;
			$this->version->created_id  = $this->_auth['id'];
			$this->version->created_at  = $datetime;
			$this->version->updated_id  = $this->_auth['id'];
			$this->version->updated_at  = $datetime;

  		if ($this->version->save() == false) {
  			//バリデーションエラー内容
  			foreach ($this->version->getMessages() as $message) {
  				$errorMsg[$message->getField()] = $message->getMessage();
  			}

  			$this->view->errorMsg 	= $errorMsg;
				$this->view->name 	  	= $name;
				$this->view->related_os = $related_os;

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
			$version = $this->version->getVersionInfo($id);

			//delete_flgのステータスをONにする
			$version->delete_flg = $this->config->define->invalid;
			$version->updated_id = $this->_auth['id'];
			$version->updated_at = date('Y-m-d H:i:s');

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
	public function successAction()
	{
		parent::successRedirect($this->request->isPost(), 'version');
	}
}
