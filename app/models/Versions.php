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

        //$config = new Phalcon\Config\Adapter\Ini(APP_PATH . "app/config/config.ini");

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
            'message' 	=> $this->getDI()->get('config')->validates_versions->presence_name
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
                'message' => $this->getDI()->get('config')->validates_versions->uniq_name
            )));
        }

        if ($this->validationHasFailed() == true) {
            return false;
        }
    }
}
