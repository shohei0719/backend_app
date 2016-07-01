<?php

class TerminalController extends ControllerBase
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

			//キャリア一覧取得
			$criteria = Carriers::query();
			$criteria->andwhere('delete_flg = :delete_flg:', ['delete_flg' => 0]);
			$carriers = $criteria->execute();

			//メーカー一覧取得
			$criteria = Makers::query();
			$criteria->andwhere('delete_flg = :delete_flg:', ['delete_flg' => 0]);
			$makers = $criteria->execute();

			//OS一覧取得
			$criteria = Oss::query();
			$criteria->andwhere('delete_flg = :delete_flg:', ['delete_flg' => 0]);
			$oss = $criteria->execute();

			//バージョン一覧取得
			/*
			$criteria = Versions::query();
			$criteria->andwhere('delete_flg = :delete_flg:', ['delete_flg' => 0]);
			$versions = $criteria->execute();
			*/

			//組織一覧取得
			$criteria = Organizations::query();
			$criteria->andwhere('delete_flg = :delete_flg:', ['delete_flg' => 0]);
			$organizations = $criteria->execute();

			$this->view->carriers 			= $carriers;
			$this->view->makers 				= $makers;
			$this->view->oss 						= $oss;
			//$this->view->versions 			= $versions;
			$this->view->organizations 	= $organizations;

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
			$criteria = Terminals::query();
			$criteria->columns('Terminals.id as id, Terminals.name as name, os.name as os_name, version.name as version_name, Terminals.tel, Terminals.mail');
			$criteria->leftJoin('Oss', 'os.id = Terminals.os', 'os');
			$criteria->leftJoin('Makers', 'maker.id = Terminals.maker', 'maker');
			$criteria->leftJoin('Versions', 'version.id = Terminals.version', 'version');
			$criteria->leftJoin('Carriers', 'carrier.id = Terminals.carrier', 'carrier');
			$criteria->leftJoin('Organizations', 'organization.id = Terminals.organization', 'organization');
			$criteria->andwhere('Terminals.delete_flg = :delete_flg:', ['delete_flg' => 0]);
			$criteria->andwhere('os.delete_flg = :delete_flg:', ['delete_flg' => 0]);
			$criteria->andwhere('maker.delete_flg = :delete_flg:', ['delete_flg' => 0]);
			$criteria->andwhere('version.delete_flg = :delete_flg:', ['delete_flg' => 0]);
			$criteria->andwhere('carrier.delete_flg = :delete_flg:', ['delete_flg' => 0]);
			$criteria->andwhere('organization.delete_flg = :delete_flg:', ['delete_flg' => 0]);

			$terminals = $criteria->execute();
//$this->logger->info(print_r($versions,1));
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
		$terminal = Terminals::findFirst(array(
			"(id = :id:)",
			'bind' => array('id' => $id)
		));

		//作成者情報を取得
		$created_admin = Admins::findFirst(array(
			"(id = :id:)",
			'bind' => array('id' => $terminal->created_id)
		));

		//更新者情報を取得
		$updated_admin = Admins::findFirst(array(
			"(id = :id:)",
			'bind' => array('id' => $terminal->updated_id)
		));

		//OSテーブルから取得
		$criteria = Oss::query();
		$criteria->andwhere('delete_flg = :delete_flg:', ['delete_flg' => 0]);
		$oss = $criteria->execute();

		//バージョンテーブルから取得
		$criteria = Versions::query();
		$criteria->andwhere('delete_flg = :delete_flg:', ['delete_flg' => 0]);
		$versions = $criteria->execute();

		//キャリアテーブルから取得
		$criteria = Carriers::query();
		$criteria->andwhere('delete_flg = :delete_flg:', ['delete_flg' => 0]);
		$carriers = $criteria->execute();

		//メーカーテーブルから取得
		$criteria = Makers::query();
		$criteria->andwhere('delete_flg = :delete_flg:', ['delete_flg' => 0]);
		$makers = $criteria->execute();

		//組織テーブルから取得
		$criteria = Organizations::query();
		$criteria->andwhere('delete_flg = :delete_flg:', ['delete_flg' => 0]);
		$organizations = $criteria->execute();

		//Viewに渡す
		$this->view->oss = $oss;
		$this->view->versions = $versions;
		$this->view->carriers = $carriers;
		$this->view->makers = $makers;
		$this->view->organizations = $organizations;

		/*
		 * statusチェック
		 * 0 : 一覧から遷移
		 * 1 : 編集から遷移
		 */
		switch($this->request->getPost('status')){
			//一覧から遷移
			case 0:

				$this->view->terminal = $terminal;
				$this->view->created_admin = $created_admin;
				$this->view->updated_admin = $updated_admin;

				break;

			//編集から遷移
			case 1:
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
$this->logger->info($thumb);

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
							,'action' => 'success'
						)
					);
				}

				break;
		}
	}

	//新規
	public function newAction(){

    //OSテーブルから取得
		$criteria = Oss::query();
		$criteria->andwhere('delete_flg = :delete_flg:', ['delete_flg' => 0]);
		$oss = $criteria->execute();

		//バージョンテーブルから取得
		$criteria = Versions::query();
		$criteria->andwhere('delete_flg = :delete_flg:', ['delete_flg' => 0]);
		$versions = $criteria->execute();

		//キャリアテーブルから取得
		$criteria = Carriers::query();
		$criteria->andwhere('delete_flg = :delete_flg:', ['delete_flg' => 0]);
		$carriers = $criteria->execute();

		//メーカーテーブルから取得
		$criteria = Makers::query();
		$criteria->andwhere('delete_flg = :delete_flg:', ['delete_flg' => 0]);
		$makers = $criteria->execute();

		//組織テーブルから取得
		$criteria = Organizations::query();
		$criteria->andwhere('delete_flg = :delete_flg:', ['delete_flg' => 0]);
		$organizations = $criteria->execute();

		//Viewに渡す
		$this->view->oss = $oss;
		$this->view->versions = $versions;
		$this->view->carriers = $carriers;
		$this->view->makers = $makers;
		$this->view->organizations = $organizations;

//$this->logger->info($oss);
		if ($this->request->isPost() == true) {
    	$terminal = new Terminals();

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

			$terminal->created_id  	= $this->_auth['id'];
			$terminal->created_at  	= $datetime;
			$terminal->updated_id  	= $this->_auth['id'];
			$terminal->updated_at  	= $datetime;

    		if ($terminal->save() == false) {
    			//バリデーションエラー内容
    			foreach ($terminal->getMessages() as $message) {
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
			//IDから管理者情報取得
			$terminal = Terminals::findFirst(array(
				"(id = :id:)",
				'bind' => array('id' => $id)
			));
			//delete_flgのステータスをONにする
			$terminal->delete_flg = 1;
			$terminal->updated_id  = $this->_auth['id'];
			$terminal->updated_at  = date('Y-m-d H:i:s');

			if ($terminal->save() == false) {
				foreach ($terminal->getMessages() as $message) {
					$errorMsg[$message->getField()] = $message->getMessage();
				}
//$this->logger->info(print_r($errorMsg,1));
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
	public function successAction(){
		//編集画面から遷移時のみ表示
    	if($this->request->isPost() == false){
    		$this->response->redirect('/backend_app/');
    	}
	}
}
