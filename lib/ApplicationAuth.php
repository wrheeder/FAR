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
    function verifyCredentials($user, $password) {
        $valid = parent::verifyCredentials($user, $password);
        if($valid){
            $login_hist=$this->add('Model_LoginHistory');
            $log = array('user_id'=>$valid,'action'=>'login','date'=>  date("Y-m-d H:i:s"),'ip'=>$this->getRealIpAddr());
            $login_hist->set($log)->save();
        }
        return $valid;
    }
    
    function logout() {
        $user_id = $this->api->auth->get('id');
        $valid = parent::logout();
        if($valid){
            $login_hist=$this->add('Model_LoginHistory');
            $log = array('user_id'=>$user_id,'action'=>'logout','date'=>  date("Y-m-d H:i:s"),'ip'=>$this->getRealIpAddr());
            $login_hist->set($log)->save();
        }
        return $valid;
    }
    function isAdmin() {
        if ($this->get('isAdmin'))
            return true;
        else
            return false;
    }

    function hasPartsCatalogueBrowser() {
        if ($this->get('pc_search'))
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

    function getRealIpAddr() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        //check ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        //to check ip is pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

}