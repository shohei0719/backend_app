<?php

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Validator\Uniqueness as UniquenessValidator;
use Phalcon\Mvc\Model\Validator\PresenceOf as PresenceOfValidator;
use Phalcon\Mvc\Model\Message;

class Versions extends Model
{

    public function initilize()
    {

        //adminsテーブル
        $this->setSource('versions');

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
            'message' 	=> $this->getDI()->get('message')->validates_versions->presence_name
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
                'field' => 'name',
                'message' => $this->getDI()->get('message')->validates_versions->uniq_name
            )));
        }

        if ($this->validationHasFailed() == true) {
            return false;
        }
    }

    /*
		 * 検索結果を返す
		 * @param $related_os
		 * @param $name
		 * @return $versions 検索結果
		 */
		public function getSearchResult($related_os, $name)
		{
      $criteria = Versions::query();
			$criteria->columns('Versions.id, o.name as os_name, Versions.name');
			$criteria->leftJoin('Oss', 'o.id = Versions.related_os', 'o');

			//related_osがPostされているとき
			if(!empty($related_os)){
				$criteria->andwhere('Versions.related_os = :related_os:', ['related_os' => $related_os]);
			}

			//nameがPostされているとき
			if(!empty($name)){
				$criteria->andwhere('Versions.name LIKE :name:', ['name' => '%' . $name . '%']);
			}

			$criteria->andwhere('Versions.delete_flg = :delete_flg:', ['delete_flg' => $this->getDI()->get('config')->define->valid]);
			$criteria->andwhere('o.delete_flg = :delete_flg:', ['delete_flg' => $this->getDI()->get('config')->define->valid]);
			$versions = $criteria->execute();

      return $versions;
		}

		/*
		 * 全結果を返す
		 * @return $oss 全結果
		 */
		public function getAllResult()
		{
      $criteria = Versions::query();
			$criteria->columns('Versions.id, o.name as os_name, Versions.name');
			$criteria->leftJoin('Oss', 'o.id = Versions.related_os', 'o');
			$criteria->andwhere('Versions.delete_flg = :delete_flg:', ['delete_flg' => $this->getDI()->get('config')->define->valid]);
			$criteria->andwhere('o.delete_flg = :delete_flg:', ['delete_flg' => $this->getDI()->get('config')->define->valid]);

			$versions = $criteria->execute();

      return $versions;
		}

		/*
		 * IDで検索してOS情報を返す
		 * @param $id
		 * @return $versions 全結果
		 */
		public function getVersionInfo($id)
		{
			$version = Versions::findFirst(array(
				"(id = :id:)",
				'bind' => array('id' => $id)
			));

			return $version;
		}
}
