<?php

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Validator\Uniqueness as UniquenessValidator;
use Phalcon\Mvc\Model\Validator\PresenceOf as PresenceOfValidator;
use Phalcon\Mvc\Model\Message;

class Organizations extends Model
{

    public function initilize()
    {

      //adminsテーブル
      $this->setSource('organizations');

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
            'message' => $this->getDI()->get('message')->validates_organizations->presence_name
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
              'field'   => 'name',
              'message' => $this->getDI()->get('message')->validates_organizations->uniq_name
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
     * @return $organizations 検索結果
     */
    public function getSearchResult($name)
    {
      $criteria = Organizations::query();

      if(!empty($name)){
        $criteria->andwhere('name LIKE :name:', ['name' => '%' . $name . '%']);
      }

      $criteria->andwhere('delete_flg = :delete_flg:', ['delete_flg' => $this->getDI()->get('config')->define->valid]);
      $organizations = $criteria->execute();

      return $organizations;
    }

    /*
     * 全結果を返す
     * @return $organizations 全結果
     */
    public function getAllResult()
    {
      $criteria = Organizations::query();
      $criteria->andwhere('delete_flg = :delete_flg:', ['delete_flg' => $this->getDI()->get('config')->define->valid]);
      $organizations = $criteria->execute();

      return $organizations;
    }

    /*
     * IDで検索して組織情報を返す
     * @param $id
     * @return $organization 全結果
     */
    public function getOrganizationInfo($id)
    {
      $organization = Organizations::findFirst(array(
        "(id = :id:)",
        'bind' => array('id' => $id)
      ));

      return $organization;
    }
}
