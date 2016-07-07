<?php

class MakerController extends ControllerBase
{

	public function initialize()
	{
		parent::initialize();
		/*
		 * ログイン状態をチェック
		 */
	 	$_auth = parent::checkRooting('maker');

		$this->maker = new Makers();
		$this->admin = new Admins();
	}

	//一覧
  public function indexAction()
  {

  	$currentPage = ($_GET['page']) ? (int) $_GET['page'] : 1;

		if($_GET['name']){ //Searchのときの処理

			$name = ($_GET['name']) ? (string) substr($_GET['name'], 0, -1) : ''; //最後に「/」が入るので削除

			//検索結果取得
			$makers = $this->maker->getSearchResult($name);

			//Viewに渡す
			$this->view->name = $name;

		} else {

			$makers = $this->maker->getAllResult();

		}

  	$paginator = new Phalcon\Paginator\Adapter\Model(array(
  			"data" => $makers,
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
		$maker = $this->maker->getMakerInfo($id);
		$created_admin = $this->admin->getAdminInfo($maker->created_id);
		$updated_admin = $this->admin->getAdminInfo($maker->updated_id);

		/*
		 * statusチェック
		 * 1 : 編集から遷移 ($this->config->define->edit)
		 */
		switch($this->request->getPost('status')){

			//編集から遷移
			case $this->config->define->edit:
				//Post Params
				$name = $this->request->getPost('name');

				$maker->name        = $name;
				$maker->updated_id  = $this->_auth['id'];
				$maker->updated_at  = date('Y-m-d H:i:s');

				if ($maker->save() == false) {

					//バリデーションエラー内容
					foreach ($maker->getMessages() as $message) {
						$errorMsg[$message->getField()] = $message->getMessage();
					}

					//Viewに渡す
					$this->view->errorMsg  	   = $errorMsg;
					$this->view->maker         = $maker;
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

			//一覧から遷移
			default:

				$this->view->maker = $maker;
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
			$this->maker->name     	= $name;
			$this->maker->created_id  = $this->_auth['id'];
			$this->maker->created_at  = $datetime;
			$this->maker->updated_id  = $this->_auth['id'];
			$this->maker->updated_at  = $datetime;

  		if ($this->maker->save() == false) {
  			//バリデーションエラー内容
  			foreach ($this->maker->getMessages() as $message) {
  				$errorMsg[$message->getField()] = $message->getMessage();
  			}

    		$this->view->errorMsg 	= $errorMsg;
				$this->view->name 	 		= $name;

  		} else {
  			$this->dispatcher->forward(
  				array(
  					 'controller' => 'Maker'
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
			$maker = $this->maker->getMakerInfo($id);

			//delete_flgのステータスをONにする
			$maker->delete_flg = $this->config->define->invalid;;
			$maker->updated_id = $this->_auth['id'];
			$maker->updated_at = date('Y-m-d H:i:s');

			if ($maker->save() == false) {
				foreach ($maker->getMessages() as $message) {
					$errorMsg[$message->getField()] = $message->getMessage();
				}
			} else {
				$this->dispatcher->forward(
  				array(
  					 'controller' => 'Maker'
  					,'action'     => 'success'
  				)
  			);
			}
		}
	}

	//成功時
	public function successAction()
	{
		parent::successRedirect($this->request->isPost(), 'maker');
	}
}
