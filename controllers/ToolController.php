<?php
namespace controllers;

class ToolController {
    
    // 切换用户
    public function users() {

        $model = new \models\User;
        $data = $model->getAll();
        echo json_encode([
            'status_code' => 200,
            'data' => $data,
        ]);
    }


    // 刷新页面（切换用户）
    public function login() {

        // 如果当前是开发模式，才能访问
        if(config('mode') != 'dev') {
            
            die('非法访问');
        }

        $email = $_GET['email'];
        // 退出
        $_SESSION = [];
        // 重新登录
        $user = new \models\User;
        $user->login($email, md5('123123'));
    }
}