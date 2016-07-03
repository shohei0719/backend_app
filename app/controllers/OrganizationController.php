<?php

class OrganizationController extends ControllerBase
{

	public function initialize()
	{
		parent::initialize();
		/*
		 * ログイン状態をチェック
		 */
	 	$_auth = parent::checkRooting('organization');

		$this->organization = new Organizations();
		$this->admin = new Admins();
	}

	//一覧
  public function indexAction()
  {

  	$currentPage = ($_GET['page']) ? (int) $_GET['page'] : 1;

		if($_GET['name']){ //Searchのときの処理

			$name = ($_GET['name']) ? (string) substr($_GET['name'], 0, -1) : ''; //最後に「/」が入るので削除

			//検索結果取得
			$organization = $this->organization->getSearchResult($name);

			//Viewに渡す
			$this->view->name = $name;

		} else {

			$organizations = $this->organization->getAllResult();

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
		$organization  = $this->organization->getOrganizationInfo($id);
		$created_admin = $this->admin->getAdminInfo($carrier->created_id);
		$updated_admin = $this->admin->getAdminInfo($carrier->updated_id);

		/*
		 * statusチェック
		 * 0 : 一覧から遷移
		 * 1 : 編集から遷移
		 */
		switch($this->request->getPost('status')){
			//一覧から遷移
			case $this->config->define->list:

				$this->view->organization = $organization;
				$this->view->created_admin = $created_admin;
				$this->view->updated_admin = $updated_admin;

				break;

			//編集から遷移
			case $this->config->define->edit:
				//Post Params
				$name = $this->request->getPost('name');

				$organization->name        = $name;
				$organization->updated_id  = $this->_auth['id'];
				$organization->updated_at  = date('Y-m-d H:i:s');

				if ($organization->save() == false) {

					//バリデーションエラー内容
					foreach ($organization->getMessages() as $message) {
						$errorMsg[$message->getField()] = $message->getMessage();
					}

					//Viewに渡す
					$this->view->errorMsg  	   = $errorMsg;
					$this->view->organization  = $organization;
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

  		//Post Params
  		$name	= $this->request->getPost('name');

			//現在時刻取得
			$datetime = date('Y-m-d H:i:s');

			//キャリアデータ登録
			$this->organization->name     	= $name;
			$this->organization->created_id = $this->_auth['id'];
			$this->organization->created_at = $datetime;
			$this->organization->updated_id = $this->_auth['id'];
			$this->organization->updated_at = $datetime;

  		if ($this->organization->save() == false) {
  			//バリデーションエラー内容
  			foreach ($this->organization->getMessages() as $message) {
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
	public function successAction()
	{
		parent::successRedirect($this->request->isPost(), 'organization');
	}
}
