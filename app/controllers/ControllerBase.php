<?php

use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;
use Phalcon\Config\Adapter\Ini as ConfigIni;

class ControllerBase extends Controller
{

	protected $_auth = '';

  /*
	 * session
	 */
		public function _registerSession(Admins $admin){
			$this->session->set('auth', array(
				'id'    	 => $admin->id,
				'mail' 		 => $admin->mail,
				'name'  	 => $admin->name,
				'permission' => $admin->permission
			));
		}

    protected function initialize()
    {
			//最後に/がない場合はリダイレクト
			if(substr($_SERVER['REQUEST_URI'], -1) != '/'){
				$this->response->redirect($_SERVER['REQUEST_URI'] . '/');
			}

    	//Viewに管理者情報を渡す
    	$this->setAuth();

    	if(!empty($this->_auth)){
				$this->view->auth_id 		 = $this->_auth['id'];
    		$this->view->auth_name 		 = $this->_auth['name'];
				$this->view->auth_permission = $this->_auth['permission'];
    	}
    }

    public function setAuth()
    {
    	$this->_auth = $this->session->get('auth');
    }

    public function getAuth()
    {
    	return $this->_auth;
    }
}
