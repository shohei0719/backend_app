<?php

class TerminalController extends ControllerBase
{

	public function initialize()
	{
		parent::initialize();
		/*
		 * ログイン状態をチェック
		 */
	 	$_auth = parent::checkRooting('terminal');

		$this->terminal = new Terminals();
		$this->admin 		= new Admins();
		$this->carrier  = new Carriers();
		$this->maker		= new Makers();
		$this->os				= new Oss();
		$this->organization = new Organizations();
		$this->version  = new Versions();
	}

	//一覧
  public function indexAction()
  {

  	$currentPage = ($_GET['page']) ? (int) $_GET['page'] : 1;

		//キャリア一覧取得
		$carriers = $this->carrier->getAllResult();

		//メーカー一覧取得
		$makers = $this->maker->getAllResult();

		//OS一覧取得
		$oss = $this->os->getAllResult();

		//組織一覧取得
		$organizations = $this->organization->getAllResult();

		$this->view->carriers 			= $carriers;
		$this->view->makers 				= $makers;
		$this->view->oss 						= $oss;
		$this->view->organizations 	= $organizations;

		if($_GET['carrier'] or $_GET['maker'] or $_GET['os'] or $_GET['organization'] or $_GET['name']){ //Searchのときの処理

			$carrier 			= ($_GET['carrier']) ? $_GET['carrier'] : 0;
			$maker 				= ($_GET['maker']) ? $_GET['maker'] : 0;
			$os 					= ($_GET['os']) ? $_GET['os'] : 0;
			$organization = ($_GET['organization']) ? $_GET['organization'] : 0;
			$name 				= ($_GET['name']) ? (string) substr($_GET['name'], 0, -1) : ''; //最後に「/」が入るので削除

			$versions = $this->terminal->getSearchResult($carrier, $maker, $os, $organization, $name);

			//Viewに渡す
			$this->view->carrier 			= $carrier;
			$this->view->maker 				= $maker;
			$this->view->os 					= $os;
			$this->view->organization = $organization;
			$this->view->name 				= $name;

		} else {

			$terminals = $this->terminal->getAllResult();

		}

  	$paginator = new Phalcon\Paginator\Adapter\Model(array(
  			"data" => $terminals,
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
		$terminal = $this->terminal->getTerminalInfo($id);
		$created_admin = $this->admin->getAdminInfo($terminal->created_id);
		$updated_admin = $this->admin->getAdminInfo($terminal->updated_id);

		//OSテーブルから取得
		$oss = $this->os->getAllResult();

		//バージョンテーブルから取得
		$versions = $this->version->getAllResult();

		//キャリアテーブルから取得
		$carriers = $this->carrier->getAllResult();

		//メーカーテーブルから取得
		$makers = $this->maker->getAllResult();

		//組織テーブルから取得
		$organizations = $this->organization->getAllResult();

		//Viewに渡す
		$this->view->oss = $oss;
		$this->view->versions = $versions;
		$this->view->carriers = $carriers;
		$this->view->makers = $makers;
		$this->view->organizations = $organizations;

		/*
		 * statusチェック
		 * 1 : 編集から遷移 ($this->config->define->edit)
		 */
		switch($this->request->getPost('status')){

			//編集から遷移
			case $this->config->define->edit:
				//Post Params
				$carrier 	   	 	= $this->request->getPost('carrier');
			 	$maker				 	= $this->request->getPost('maker');
				$os						 	= $this->request->getPost('os');
				$version				= $this->request->getPost('version');
				$name					 	= $this->request->getPost('name');
				//$thumb				 	= $this->request->getPost('images[]');
				$organization		= $this->request->getPost('organization');
				$mail						= $this->request->getPost('mail');
				$tel 						= $this->request->getPost('tel');
				$comment				= $this->request->getPost('comment');

				//拡張子取得
				$info 	= new SplFileInfo($_FILES["image"]["name"]);
				$thumb  = uniqid() . "." . $info->getExtension();
//$this->logger->info($thumb);

				//キャリアデータ登録
				$terminal->name     	  = $name;
				$terminal->carrier			= $carrier;
				$terminal->maker				= $maker;
				$terminal->os						= $os;
				$terminal->version			= $version;
				$terminal->name					= $name;
				//$terminal->thumb				= $thumb;
				$terminal->organization = $organization;
				$terminal->tel 					= $tel;
				$terminal->mail					= $mail;
				$terminal->comment  		= $comment;
				$terminal->image				= $thumb;
				$terminal->rental_user	= $this->_auth['id'];

				$terminal->updated_id  = $this->_auth['id'];
				$terminal->updated_at  = date('Y-m-d H:i:s');

				if ($terminal->save() == false) {

					//バリデーションエラー内容
					foreach ($terminal->getMessages() as $message) {
						$errorMsg[$message->getField()] = $message->getMessage();
					}
					$this->logger->info(print_r($errorMsg,1));
					//Viewに渡す
					$this->view->errorMsg  	   = $errorMsg;
					$this->view->terminal     = $terminal;
					$this->view->created_admin = $created_admin;
					$this->view->updated_admin = $updated_admin;

				} else {

					//画像の保存
					if (is_uploaded_file($_FILES["image"]["tmp_name"])) {
						if (move_uploaded_file($_FILES["image"]["tmp_name"], "/Applications/MAMP/htdocs/backend_app/public/img/images/" . $thumb)) {
							chmod("/Applications/MAMP/htdocs/terminal/public/img/users_img/" . $thumb, 0644);
							$this->logger->info($thumb . "をアップロードしました。");
						} else {
							$this->logger->info("アップロードに失敗しました。");
						}
					}

					$this->dispatcher->forward(
						array(
							'controller' => 'Terminal'
							,'action' 	 => 'success'
						)
					);
				}
				break;

			//一覧から遷移
			default:

				$this->view->terminal = $terminal;
				$this->view->created_admin = $created_admin;
				$this->view->updated_admin = $updated_admin;

				break;
		}
	}

	//新規
	public function newAction(){

    //OSテーブルから取得
		$oss = $this->os->getAllResult();

		//バージョンテーブルから取得
		$versions = $this->version->getAllResult();

		//キャリアテーブルから取得
		$carriers = $this->carrier->getAllResult();

		//メーカーテーブルから取得
		$makers = $this->maker->getAllResult();

		//組織テーブルから取得
		$organizations = $this->organization->getAllResult();

		//Viewに渡す
		$this->view->oss = $oss;
		$this->view->versions = $versions;
		$this->view->carriers = $carriers;
		$this->view->makers = $makers;
		$this->view->organizations = $organizations;

		if ($this->request->isPost() == true) {

    	//Post Params
    	$carrier 	   	 	= $this->request->getPost('carrier');
		 	$maker				 	= $this->request->getPost('maker');
			$os						 	= $this->request->getPost('os');
			$version				= $this->request->getPost('version');
			$name					 	= $this->request->getPost('name');
			$thumb				 	= $this->request->getPost('thumb');
			$organization		= $this->request->getPost('organization');
			$mail						= $this->request->getPost('mail');
			$tel 						= $this->request->getPost('tel');
			$comment				= $this->request->getPost('comment');
			//$image 					= $this->request->getPost('image');

//$this->logger->info($related_os);

			//現在時刻取得
			$datetime = date('Y-m-d H:i:s');

			//キャリアデータ登録
			$this->terminal->name     	  = $name;
			$this->terminal->carrier			= $carrier;
			$this->terminal->maker				= $maker;
			$this->terminal->os						= $os;
			$this->terminal->version			= $version;
			$this->terminal->name					= $name;
			//$terminal->thumb				= $thumb;
			$this->terminal->organization = $organization;
			$this->terminal->tel 					= $tel;
			$this->terminal->mail					= $mail;
			$this->terminal->comment  		= $comment;
			$this->terminal->image				= $thumb;
			$this->terminal->rental_user	= $this->_auth['id'];

			$this->terminal->created_id  	= $this->_auth['id'];
			$this->terminal->created_at  	= $datetime;
			$this->terminal->updated_id  	= $this->_auth['id'];
			$this->terminal->updated_at  	= $datetime;

  		if ($this->terminal->save() == false) {
  			//バリデーションエラー内容
  			foreach ($this->terminal->getMessages() as $message) {
  				$errorMsg[$message->getField()] = $message->getMessage();
  			}

  			$this->view->errorMsg 			 = $errorMsg;
				$this->view->name 	  			 = $name;
				$this->view->carrier_id			 = $carrier;
				$this->view->maker_id				 = $maker;
				$this->view->os_id					 = $os;
				$this->view->version_id			 = $version;
				$this->view->image					 = $thumb;
				$this->view->organization_id = $organization;
				$this->view->tel 						 = $tel;
				$this->view->mail				 		 = $mail;
				$this->view->comment				 = $comment;

  		} else {
  			$this->dispatcher->forward(
  				array(
  					 'controller' => 'Terminal'
  					,'action'     => 'success'
  				)
  			);
  		}
  	}
	}

	public function deleteAction(){
		$id = ($_GET['id']) ? (int) $_GET['id'] : 0;

		if(!empty($id) && $id > 0){
			//IDから端末情報を取得
			$terminal = $this->terminal->getTerminalInfo();

			//delete_flgのステータスをONにする
			$terminal->delete_flg = $this->config->define->invalid;
			$terminal->updated_id = $this->_auth['id'];
			$terminal->updated_at = date('Y-m-d H:i:s');

			if ($terminal->save() == false) {
				foreach ($terminal->getMessages() as $message) {
					$errorMsg[$message->getField()] = $message->getMessage();
				}
			} else {
				$this->dispatcher->forward(
  				array(
  					 'controller' => 'Terminal'
  					,'action'     => 'success'
  				)
  			);
			}
		}
	}

	//成功時
	public function successAction()
	{
		parent::successRedirect($this->request->isPost(), 'terminal');
	}
}
