<?php

class CarrierController extends ControllerBase
{
	
	//private $admin;
	
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

			$criteria = Carriers::query();
			
			if(!empty($name)){
$this->logger->info($name);
				$criteria->andwhere('name LIKE :name:', ['name' => '%' . $name . '%']);
			}

			$criteria->andwhere('delete_flg = :delete_flg:', ['delete_flg' => 0]);
			$carriers = $criteria->execute();
			
			//Viewに渡す
			$this->view->name = $name;
			
		} else {
			$criteria = Carriers::query();
			$criteria->andwhere('delete_flg = :delete_flg:', ['delete_flg' => 0]);
			$carriers = $criteria->execute();
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
		$carrier = Carriers::findFirst(array(
			"(id = :id:)",
			'bind' => array('id' => $id)
		));
		
		//作成者情報を取得
		$created_admin = Admins::findFirst(array(
			"(id = :id:)",
			'bind' => array('id' => $carrier->created_id)
		));
		
		//更新者情報を取得
		$updated_admin = Admins::findFirst(array(
			"(id = :id:)",
			'bind' => array('id' => $carrier->updated_id)
		));
		
		/*
		 * statusチェック
		 * 0 : 一覧から遷移
		 * 1 : 編集から遷移
		 */
		switch($this->request->getPost('status')){
			//一覧から遷移
			case 0:
	
				$this->view->carrier = $carrier;
				$this->view->created_admin = $created_admin;
				$this->view->updated_admin = $updated_admin;
				
				break;
				
			//編集から遷移
			case 1:
				//Post Params
				$name = $this->request->getPost('name');

				$carrier->name        = $name;
				$carrier->updated_id  = $this->_auth['id'];
				$carrier->updated_at  = date('Y-m-d H:i:s');
				
				if ($carrier->save() == false) {

					//バリデーションエラー内容
					foreach ($admin->getMessages() as $message) {
						$errorMsg[$message->getField()] = $message->getMessage();
					}
					$this->logger->info(print_r($errorMsg,1));
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
		}
	}
	
	//新規
	public function newAction(){
		
		if ($this->request->isPost() == true) {
    		$carrier = new Carriers();
    		 
    		//Post Params
    		$name 	    	= $this->request->getPost('name');
    		 
			//現在時刻取得
			$datetime = date('Y-m-d H:i:s');
			
			//キャリアデータ登録
			$carrier->name     	  = $name;
			$carrier->created_id  = $this->_auth['id'];
			$carrier->created_at  = $datetime;
			$carrier->updated_id  = $this->_auth['id'];
			$carrier->updated_at  = $datetime;
    		 
    		if ($carrier->save() == false) {
    			//バリデーションエラー内容
    			foreach ($carrier->getMessages() as $message) {
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
			//IDから管理者情報取得
			$carrier = Carriers::findFirst(array(
				"(id = :id:)",
				'bind' => array('id' => $id)
			));
			//delete_flgのステータスをONにする
			$carrier->delete_flg = 1;
			$carrier->updated_id  = $this->_auth['id'];
			$carrier->updated_at  = date('Y-m-d H:i:s');
			
			if ($carrier->save() == false) {
				foreach ($carrier->getMessages() as $message) {
					$errorMsg[$message->getField()] = $message->getMessage();
				}
$this->logger->info(print_r($errorMsg,1));
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
	public function successAction(){
		//編集画面から遷移時のみ表示
    	if($this->request->isPost() == false){
    		$this->response->redirect('/backend_app/');
    	}
	}
	
	//Ajax
	public function ajaxAction(){
		//ajaxの場合
		if($this->request->isAjax() == true){
			
			$page = $this->request->getPost('page');
$this->logger->info($page);
			$status = 200;
			$description = 'OK';
			$headers = array();
			$contentType = 'application/json';
			
			$this->response->setContentType($contentType, 'UTF-8');
			
		}
	}
}
