<?php

class IndexController extends ControllerBase
{

	public function initialize()
	{
		parent::initialize();
		/*
		 * ログイン状態をチェック
		 */
	 	$_auth = parent::checkRooting('index');
	}

	public function indexAction()
	{

	}
}
