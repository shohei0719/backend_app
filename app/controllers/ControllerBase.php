<?php

use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;
use Phalcon\Config\Adapter\Ini as ConfigIni;

class ControllerBase extends Controller
{

	protected $_auth = '';

  /*
	 * session登録
	 */
	public function _registerSession(Admins $admin){
		$this->session->set('auth', array(
			'id'    	 		=> $admin->id,
			'mail' 		 		=> $admin->mail,
			'name'  	 		=> $admin->name,
			'permission' 	=> $admin->permission
		));
	}

	/*
	 * session削除
	 */
	public function _removeSession(){
		$this->session->destroy();
	}

  protected function initialize()
  {
		/*
		 * 最後に/がない場合はリダイレクト
		 * (本来は.htaccessで設定するべきなのだが、うまくできなかった)
		 */
		if(substr($_SERVER['REQUEST_URI'], -1) != '/'){
			$this->response->redirect($_SERVER['REQUEST_URI'] . '/');
		}

  	//Viewに管理者情報を渡す
  	$this->setAuth();

  	if(!empty($this->_auth)){
			$this->view->auth_id 		 			= $this->_auth['id'];
  		$this->view->auth_name 		 		= $this->_auth['name'];
			$this->view->auth_permission 	= $this->_auth['permission'];
  	}
  }

	/*
	 * Session情報 Setter
	 */
  public function setAuth()
  {
  	$this->_auth = $this->session->get('auth');
  }

	/*
	 * Session情報 Getter
	 */
  public function getAuth()
  {
  	return $this->_auth;
  }

	/*
	 * 管理者の権限をチェック　Redirect
	 * @param $view_directry_name
	 * @param $id
	 * @return array($auth)
	 */
	public function checkRooting($view_directry_name, $id = null)
	{
		if(!empty($this->getAuth())){
			$_auth = $this->getAuth();

			switch($view_directry_name){
				case 'admin':
					//管理者情報編集権限がない場合は、user画面にリダイレクト
					if($_auth['permission'] != 1){
						if(!preg_match('/edit/', $_SERVER['REQUEST_URI']) and !preg_match('/change/', $_SERVER['REQUEST_URI'])){
							$this->response->redirect('/backend_app/user/');
						} else {
							//getパラメータidと自分のidが同じでない場合は戻す
							if(!empty($id) && $id != (string) $_auth['id']){
								$this->response->redirect('/backend_app/user/');
							}
						}
					}
					break;
				case 'user':
					//管理者情報編集権限がない場合は、terminal画面にリダイレクト
					if($_auth['permission'] > 2){
						if(!preg_match('/edit/', $_SERVER['REQUEST_URI']) and !preg_match('/change/', $_SERVER['REQUEST_URI'])){
							$this->response->redirect('/backend_app/terminal/');
						}
					}
					break;
				case 'index':
					$this->response->redirect('/backend_app/admin/');
					break;
				default:
			}

			return $_auth;

		}else{
			$this->response->redirect('/backend_app/signin/');
		}
	}

	/*
	 * Success画面へ遷移した時の判定
	 * 値がPostされた時しか表示されないようにする
	 * @param $boolean
	 * @param $return_view
	 */
	public function successRedirect($boolean, $return_view)
	{
		if($boolean == false){
			$this->response->redirect('/backend_app/' . $return_view);
		}
	}
}
