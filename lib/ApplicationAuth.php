<?php

class ApplicationAuth extends BasicAuth {

    function init() {
        parent::init();

        $this->usePasswordEncryption('md5');
        $model = $this->setModel('Model_User', 'username', 'password');
    }

    function verifyCredentials($user, $password) {
        if ($user) {
            $model = $this->getModel()->tryloadBy('username', $user);
            if (!$model->isInstanceLoaded())
                return false;
            if ($this->encryptPassword($password) == $model->get('password')) {
                $this->addInfo($model->get());
                unset($this->info['password']);
                if ($model['password'] === 'ae5eb633cabdeb077de626b83ef51171') {
                    die('change pw');
                }
                return true;
            }else
                return false;
        }else
            return false;
    }

    function isAdmin() {
        $model = $this->getModel()->loadBy('id', $this->info['id']);
        if ($model['isAdmin'])
            return true; else
            return false;
    }


}