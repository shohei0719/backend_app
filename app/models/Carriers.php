<?php

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Validator\Uniqueness as UniquenessValidator;
use Phalcon\Mvc\Model\Validator\PresenceOf as PresenceOfValidator;
use Phalcon\Mvc\Model\Message;

class Carriers extends Model
{

    public function initilize()
    {

        //adminsテーブル
        $this->setSource('carriers');

    }

    /*
     * バリデーションチェック前
     * Not Nullの自動バリデーションチェックが入る前に「必須」バリデーションチェックする（ちょっと気持ち悪いので別のやり方を考えたほうがいいかもしれない）
     */
    public function beforeValidation()
    {

        //必須入力チェック
        $this->validate(new PresenceOfValidator(array(
            'field'		=> 'name',
            'message' => $this->getDI()->get('message')->validates_carriers->presence_name
        )));

        if ($this->validationHasFailed() == true) {
            return false;
        }

    }

    /*
     * バリデーション処理
     */
    public function validation()
    {
        //重複チェック
        if (empty($this->getMessages('name'))) {
            $this->validate(new UniquenessValidator(array(
                'field' 	=> 'name',
                'message' => $this->getDI()->get('message')->validates_carriers->uniq_name
            )));
        }

        if ($this->validationHasFailed() == true) {
            return false;
        }
    }

		/*
		 * 検索結果を返す
		 * @param $permission
		 * @param $name
		 * @param $mail
		 * @return $carriers 検索結果
		 */
		public function getSearchResult($name)
		{
			$criteria = Carriers::query();

			if(!empty($name)){
				$criteria->andwhere('name LIKE :name:', ['name' => '%' . $name . '%']);
			}

			$criteria->andwhere('delete_flg = :delete_flg:', ['delete_flg' => $this->getDI()->get('config')->define->valid]);
			$carriers = $criteria->execute();

			return $carriers;
		}

		/*
		 * 全結果を返す
		 * @return $carriers 全結果
		 */
		public function getAllResult()
		{
			$criteria = Carriers::query();
			$criteria->andwhere('delete_flg = :delete_flg:', ['delete_flg' => $this->getDI()->get('config')->define->valid]);
			$carriers = $criteria->execute();

			return $carriers;
		}

		/*
		 * IDで検索してキャリア情報を返す
		 * @param $id
		 * @return $carrier 全結果
		 */
		public function getCarrierInfo($id)
		{
			$carrier = Carriers::findFirst(array(
				"(id = :id:)",
				'bind' => array('id' => $id)
			));

			return $carrier;
		}
}
