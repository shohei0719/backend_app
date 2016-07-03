<?php

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Validator\Uniqueness as UniquenessValidator;
use Phalcon\Mvc\Model\Validator\PresenceOf as PresenceOfValidator;
use Phalcon\Mvc\Model\Message;

class Terminals extends Model
{

    public function initilize()
    {

        //adminsテーブル
        $this->setSource('terminals');

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
            'message' => $this->getDI()->get('message')->validates_terminal->presence_name
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
                'message' => $this->getDI()->get('message')->validates_terminal->uniq_name
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
		 * @return $terminals 検索結果
		 */
		public function getSearchResult($carrier, $maker, $os, $organization, $name)
		{
      $criteria = Terminals::query();
      $criteria->columns('Terminals.id as id, Terminals.name as name, os.name as os_name, version.name as version_name, Terminals.tel, Terminals.mail');
      $criteria->leftJoin('Oss', 'os.id = Terminals.os', 'os');
      $criteria->leftJoin('Makers', 'maker.id = Terminals.maker', 'maker');
      $criteria->leftJoin('Versions', 'version.id = Terminals.version', 'version');
      $criteria->leftJoin('Carriers', 'carrier.id = Terminals.carrier', 'carrier');
      $criteria->leftJoin('Organizations', 'organization.id = Terminals.organization', 'organization');

      //carrierがPostされているとき
      if(!empty($carrier)){
        $criteria->andwhere('Terminals.carrier = :carrier:', ['carrier' => $carrier]);
      }
      //makerがPostされているとき
      if(!empty($maker)){
        $criteria->andwhere('Terminals.maker = :maker:', ['maker' => $maker]);
      }
      //osがPostされているとき
      if(!empty($os)){
        $criteria->andwhere('Terminals.os = :os:', ['os' => $os]);
      }
      //organizationがPostされているとき
      if(!empty($organization)){
        $criteria->andwhere('Terminals.organization = :organization:', ['organization' => $organization]);
      }
      //nameがPostされているとき
      if(!empty($name)){
        $criteria->andwhere('Terminals.name = :name:', ['name' => $name]);
      }

      $criteria->andwhere('Terminals.delete_flg = :delete_flg:', ['delete_flg' => $this->getDI()->get('config')->define->valid]);
      $criteria->andwhere('os.delete_flg = :delete_flg:', ['delete_flg' => $this->getDI()->get('config')->define->valid]);
      $criteria->andwhere('maker.delete_flg = :delete_flg:', ['delete_flg' => $this->getDI()->get('config')->define->valid]);
      $criteria->andwhere('version.delete_flg = :delete_flg:', ['delete_flg' => $this->getDI()->get('config')->define->valid]);
      $criteria->andwhere('carrier.delete_flg = :delete_flg:', ['delete_flg' => $this->getDI()->get('config')->define->valid]);
      $criteria->andwhere('organization.delete_flg = :delete_flg:', ['delete_flg' => $this->getDI()->get('config')->define->valid]);

      $terminals = $criteria->execute();

			return $terminals;
		}

		/*
		 * 全結果を返す
		 * @return $terminals 全結果
		 */
		public function getAllResult()
		{
      $criteria = Terminals::query();
      $criteria->columns('Terminals.id as id, Terminals.name as name, os.name as os_name, version.name as version_name, Terminals.tel, Terminals.mail');
      $criteria->leftJoin('Oss', 'os.id = Terminals.os', 'os');
      $criteria->leftJoin('Makers', 'maker.id = Terminals.maker', 'maker');
      $criteria->leftJoin('Versions', 'version.id = Terminals.version', 'version');
      $criteria->leftJoin('Carriers', 'carrier.id = Terminals.carrier', 'carrier');
      $criteria->leftJoin('Organizations', 'organization.id = Terminals.organization', 'organization');
      $criteria->andwhere('Terminals.delete_flg = :delete_flg:', ['delete_flg' => $this->getDI()->get('config')->define->valid]);
      $criteria->andwhere('os.delete_flg = :delete_flg:', ['delete_flg' => $this->getDI()->get('config')->define->valid]);
      $criteria->andwhere('maker.delete_flg = :delete_flg:', ['delete_flg' => $this->getDI()->get('config')->define->valid]);
      $criteria->andwhere('version.delete_flg = :delete_flg:', ['delete_flg' => $this->getDI()->get('config')->define->valid]);
      $criteria->andwhere('carrier.delete_flg = :delete_flg:', ['delete_flg' => $this->getDI()->get('config')->define->valid]);
      $criteria->andwhere('organization.delete_flg = :delete_flg:', ['delete_flg' => $this->getDI()->get('config')->define->valid]);

      $terminals = $criteria->execute();

			return $terminals;
		}

		/*
		 * IDで検索して端末情報を返す
		 * @param $id
		 * @return $terminal 全結果
		 */
		public function getTerminalInfo($id)
		{
			$terminal = Terminals::findFirst(array(
				"(id = :id:)",
				'bind' => array('id' => $id)
			));

			return $terminal;
		}
}
