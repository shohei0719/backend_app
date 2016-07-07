<?php

class CarrierController extends ControllerBase
{

	//private $admin;

	public function initialize()
	{
		parent::initialize();
		/*
		 * ログイン状態をチェック
		 */
	 	$_auth = parent::checkRooting('user');

		$this->carrier = new Carriers();
		$this->admin = new Admins();
	}

	//一覧
  public function indexAction()
  {

  	$currentPage = ($_GET['page']) ? (int) $_GET['page'] : 1;

		if($_GET['name']){ //Searchのときの処理

			$name = ($_GET['name']) ? (string) substr($_GET['name'], 0, -1) : ''; //最後に「/」が入るので削除

			//検索結果取得
			$carriers = $this->carrier->getSearchResult($name);

			//Viewに渡す
			$this->view->name = $name;

		} else {

			$carriers = $this->carrier->getAllResult();

		}

  	$paginator = new Phalcon\Paginator\Adapter\Model(array(
  			"data" => $carriers,
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
		$carrier = $this->carrier->getCarrierInfo($id);
		$created_admin = $this->admin->getAdminInfo($carrier->created_id);
		$updated_admin = $this->admin->getAdminInfo($carrier->updated_id);

		/*
		 * statusチェック
		 * 1 : 編集から遷移 ($this->config->define->edit)
		 */
		switch($this->request->getPost('status')){

			//編集から遷移
			case $this->config->define->edit:
				//Post Params
				$name = $this->request->getPost('name');

				$carrier->name        = $name;
				$carrier->updated_id  = $this->_auth['id'];
				$carrier->updated_at  = date('Y-m-d H:i:s');

				if ($carrier->save() == false) {

					//バリデーションエラー内容
					foreach ($carrier->getMessages() as $message) {
						$errorMsg[$message->getField()] = $message->getMessage();
					}

					//Viewに渡す
					$this->view->errorMsg  	   = $errorMsg;
					$this->view->carrier       = $carrier;
					$this->view->created_admin = $created_admin;
					$this->view->updated_admin = $updated_admin;

				} else {

					$this->dispatcher->forward(
						array(
							'controller' => 'Carrier'
							,'action' => 'success'
						)
					);
				}
				break;

				//一覧から遷移
				default:

					$this->view->carrier = $carrier;
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
			$this->carrier->name     	  = $name;
			$this->carrier->created_id  = $this->_auth['id'];
			$this->carrier->created_at  = $datetime;
			$this->carrier->updated_id  = $this->_auth['id'];
			$this->carrier->updated_at  = $datetime;

  		if ($this->carrier->save() == false) {
  			//バリデーションエラー内容
  			foreach ($this->carrier->getMessages() as $message) {
  				$errorMsg[$message->getField()] = $message->getMessage();
  			}

  			$this->view->errorMsg 		= $errorMsg;
				$this->view->name 	  		= $name;

  		} else {
  			$this->dispatcher->forward(
  				array(
  					 'controller' => 'Carrier'
  					,'action'     => 'success'
  				)
  			);
  		}
  	}
	}

	public function deleteAction(){
		$id = ($_GET['id']) ? (int) $_GET['id'] : 0;

		if(!empty($id) && $id > 0){
			//IDからキャリア情報
			$carrier = $this->carrier->getCarrierInfo($id);

			//delete_flgのステータスをONにする
			$carrier->delete_flg 	= $this->config->define->invalid;
			$carrier->updated_id  = $this->_auth['id'];
			$carrier->updated_at  = date('Y-m-d H:i:s');

			if ($carrier->save() == false) {
				foreach ($carrier->getMessages() as $message) {
					$errorMsg[$message->getField()] = $message->getMessage();
				}
			} else {
				$this->dispatcher->forward(
  				array(
  					 'controller' => 'Carrier'
  					,'action'     => 'success'
  				)
    		);
			}
		}
	}

	//成功時
	public function successAction()
	{
		parent::successRedirect($this->request->isPost(), 'carrier');
	}
}
