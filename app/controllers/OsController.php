<?php

class OsController extends ControllerBase
{

	public function initialize()
	{
		parent::initialize();
		/*
		 * ログイン状態をチェック
		 */
	 	$_auth = parent::checkRooting('os');

		$this->os = new Oss();
		$this->admin = new Admins();
	}

	//一覧
  public function indexAction()
  {

		$currentPage = ($_GET['page']) ? (int) $_GET['page'] : 1;

		if($_GET['name']){ //Searchのときの処理

			$name = ($_GET['name']) ? (string) substr($_GET['name'], 0, -1) : ''; //最後に「/」が入るので削除

			//検索結果取得
			$oss = $this->os->getSearchResult($name);

			//Viewに渡す
			$this->view->name = $name;

		} else {

			$oss = $this->os->getAllResult();

		}

		$paginator = new Phalcon\Paginator\Adapter\Model(array(
				"data" => $oss,
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
		$os = $this->os->getOsInfo($id);
		$created_admin = $this->admin->getAdminInfo($os->created_id);
		$updated_admin = $this->admin->getAdminInfo($os->updated_id);

		/*
		 * statusチェック
		 * 1 : 編集から遷移 ($this->config->define->edit)
		 */
		switch($this->request->getPost('status')){

			//編集から遷移
			case $this->config->define->edit:
				//Post Params
				$name = $this->request->getPost('name');

				$os->name        = $name;
				$os->updated_id  = $this->_auth['id'];
				$os->updated_at  = date('Y-m-d H:i:s');

				if ($os->save() == false) {

					//バリデーションエラー内容
					foreach ($os->getMessages() as $message) {
						$errorMsg[$message->getField()] = $message->getMessage();
					}

					//Viewに渡す
					$this->view->errorMsg  	   = $errorMsg;
					$this->view->os		         = $os;
					$this->view->created_admin = $created_admin;
					$this->view->updated_admin = $updated_admin;

				} else {

					$this->dispatcher->forward(
						array(
							'controller' => 'Os'
							,'action' => 'success'
						)
					);
				}
				break;

			//一覧から遷移
			default:

				$this->view->os = $os;
				$this->view->created_admin = $created_admin;
				$this->view->updated_admin = $updated_admin;

				break;
		}
	}

	//新規
	public function newAction(){

		if ($this->request->isPost() == true) {

  		//Post Params
  		$name = $this->request->getPost('name');

			//現在時刻取得
			$datetime = date('Y-m-d H:i:s');

			//キャリアデータ登録
			$this->os->name     	= $name;
			$this->os->created_id = $this->_auth['id'];
			$this->os->created_at = $datetime;
			$this->os->updated_id = $this->_auth['id'];
			$this->os->updated_at = $datetime;

  		if ($this->os->save() == false) {
  			//バリデーションエラー内容
  			foreach ($this->os->getMessages() as $message) {
  				$errorMsg[$message->getField()] = $message->getMessage();
  			}

  			$this->view->errorMsg 	= $errorMsg;
				$this->view->name 	  	= $name;

  		} else {
  			$this->dispatcher->forward(
  				array(
  					 'controller' => 'Os'
  					,'action'     => 'success'
  				)
  			);
  		}
  	}
	}

	public function deleteAction(){
		$id = ($_GET['id']) ? (int) $_GET['id'] : 0;

		if(!empty($id) && $id > 0){
			//IDからOS者情報取得
			$os = $this->os->getOsInfo($id);

			//delete_flgのステータスをONにする
			$os->delete_flg = $this->config->define->invalid;
			$os->updated_id = $this->_auth['id'];
			$os->updated_at = date('Y-m-d H:i:s');

			if ($os->save() == false) {
				foreach ($os->getMessages() as $message) {
					$errorMsg[$message->getField()] = $message->getMessage();
				}
			} else {
				$this->dispatcher->forward(
  				array(
  					 'controller' => 'Os'
  					,'action'     => 'success'
  				)
  			);
			}
		}
	}

	//成功時
	public function successAction()
	{
		parent::successRedirect($this->request->isPost(), 'os');
	}
}
