<?php

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Validator\Email as EmailValidator;
use Phalcon\Mvc\Model\Validator\Uniqueness as UniquenessValidator;
use Phalcon\Mvc\Model\Validator\PresenceOf as PresenceOfValidator;
use Phalcon\Mvc\Model\Validator\StringLength as StringLengthValidator;
use Phalcon\Mvc\Model\Validator\Regex as RegexValidator;
use Phalcon\Mvc\Model\Message;

class Users extends Model
{
	/*
    protected $id;
    protected $email;
    protected $password;
	*/

    public function initilize()
    {

        //adminsテーブル
        $this->setSource('users');

    }

    /*
     * バリデーションチェック前
     * Not Nullの自動バリデーションチェックが入る前に「必須」バリデーションチェックする（ちょっと気持ち悪いので別のやり方を考えたほうがいいかもしれない）
     */
    public function beforeValidation()
    {

        //必須入力チェック
        $this->validate(new PresenceOfValidator(array(
            'field'		=> 'mail',
            'message' => $this->getDI()->get('message')->validates_common->presence_mail
        )));

        $this->validate(new PresenceOfValidator(array(
            'field'		=> 'password',
            'message' => $this->getDI()->get('message')->validates_common->presence_password
        )));

        //管理情報更新時/削除時にはパスワードのチェックをしない
        if(!preg_match('/edit/', $_SERVER['REQUEST_URI']) && !preg_match('/delete/', $_SERVER['REQUEST_URI'])){
            $this->validate(new PresenceOfValidator(array(
                'field'		=> 're_password',
                'message' => $this->getDI()->get('message')->validates_common->presence_re_password
            )));
        }

        $this->validate(new PresenceOfValidator(array(
            'field'		=> 'name',
            'message' => $this->getDI()->get('message')->validates_common->presence_name
        )));

        if ($this->validationHasFailed() == true) {
            return false;
        }

    }

    /*
     * バリデーションチェック後
     */
    public function afterValidation()
    {
        if(!preg_match('/edit/', $_SERVER['REQUEST_URI'])){
            //パスワードをハッシュ化
            $security = new \Phalcon\Security();
            $this->password = $security->hash($this->password);
        }
    }

    /*
     * バリデーション処理
     */
    public function validation()
    {

        //管理情報更新時/削除時にはパスワードのチェックをしない
        if(!preg_match('/edit/', $_SERVER['REQUEST_URI']) && !preg_match('/delete/', $_SERVER['REQUEST_URI'])){
            //形式チェック
            if (empty($this->getMessages('password'))) {
                $this->validate(new RegexValidator(array(
                        'field' 	=> 'password',
                        'pattern' 	=> '/^[a-zA-Z0-9]+$/',
                        'message' 	=> $this->getDI()->get('message')->validates_common->regex_password
                )));
            }

            //長さチェック
            //if ($this->validationHasFailed('password') == false) {
            if (empty($this->getMessages('password'))) {
                $this->validate(new StringLengthValidator(array(
                    'field'				=> 'password',
                    'max'				=> 20,
                    'min'				=> 8,
                    //'message'			=> '8文字以上20文字以下でパスワードをご入力ください。'
                    'messageMaximum' 	=> $this->getDI()->get('message')->validates_common->maximum_length_password,
                    'messageMinimum' 	=> $this->getDI()->get('message')->validates_common->minimum_length_password,
                )));
            }

            //パスワード・パスワード再入力の比較
            if (empty($this->getMessages('password'))) {
                if($this->password !== $this->re_password){
                    $message = new Message($this->getDI()->get('message')->validates_common->match_password, 'password');
                    $this->appendMessage($message);
                }
            }
        }

        //メールアドレスの形式チェック
        if (empty($this->getMessages('mail'))) {
            $this->validate(new EmailValidator(array(
                    'field' 	=> 'mail',
                    'message'   => $this->getDI()->get('message')->validates_common->check_mail,
                    'required' 	=> 'false'
            )));
        }

        //重複チェック
        if (empty($this->getMessages('mail'))) {
            $this->validate(new UniquenessValidator(array(
                'field' => 'mail',
                'message' => $this->getDI()->get('message')->validates_common->uniq_mail
            )));
        }

        if ($this->validationHasFailed() == true) {
            return false;
        }
    }

		/*
		 * 検索結果を返す
		 * @param $name
		 * @param $mail
		 * @return $users 検索結果
		 */
		public function getSearchResult($name, $mail)
		{
			$criteria = Users::query();

			if(!empty($name)){
				$criteria->andwhere('name LIKE :name:', ['name' => '%' . $name . '%']);
			}
			if(!empty($mail)){
				$criteria->andwhere('mail LIKE :mail:', ['mail' => '%' . $mail . '%']);
			}
			$criteria->andwhere('delete_flg = :delete_flg:', ['delete_flg' => $this->getDI()->get('config')->define->valid]);
			$users = $criteria->execute();
		}

		/*
		 * 全結果を返す
		 * @return $users 全結果
		 */
		public function getAllResult()
		{
			$criteria = Users::query();
			$criteria->andwhere('delete_flg = :delete_flg:', ['delete_flg' => $this->getDI()->get('config')->define->valid]);
			$users = $criteria->execute();

			return $users;
		}

		/*
		 * IDで検索してユーザ情報を返す
		 * @param $id
		 * @return $user 全結果
		 */
		public function getUserInfo($id)
		{
			$user = Users::findFirst(array(
				"(id = :id:)",
				'bind' => array('id' => $id)
			));

			return $user;
		}

}
