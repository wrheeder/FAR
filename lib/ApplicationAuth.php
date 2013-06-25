<?php

class ApplicationAuth extends BasicAuth {

    function init() {
        parent::init();

        $this->usePasswordEncryption('md5');
        $model = $this->setModel('Model_User', 'username', 'password');
    }

//    function verifyCredentials($user, $password) {
//        if ($user) {
//            $model = $this->getModel()->tryloadBy('username', $user);
//            if (!$model->isInstanceLoaded())
//                return false;
//            if ($this->encryptPassword($password) == $model->get('password')) {
//                $this->addInfo($model->get());
//                unset($this->info['password']);
//                if ($model['password'] === 'ae5eb633cabdeb077de626b83ef51171') {
//                    die('change pw');
//                }
//                return true;
//            }else
//                return false;
//        }else
//            return false;
//    }

    function isAdmin() {
        if ($this->get('isAdmin'))
            return true;
        else
            return false;
    }

    function hasStoreSearch() {
        if ($this->get('store_search'))
            return true;
        else
            return false;
    }

}