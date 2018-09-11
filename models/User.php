<?php
namespace models;
use PDO;
class User extends Base{

    public function add($email,$pass){
        $stmt = self::$pdo->prepare("insert into users (email,password) values(?,?)");
        return $stmt->execute([
            $email,
            $pass,
        ]);
    }


    public function login($email,$pass){
        $stmt = self::$pdo->prepare('select * from users where email = ? and password=?');
        $stmt->execute([
            $email,
            $pass,
        ]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($user){
            
            $_SESSION['id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['money'] = $user['money'];
            return true;
        }
        else{
            return false;
        }
    }


    // 为用户增加金额
    public function addMoney($money, $userId) {
        $stmt = self::$pdo->prepare("UPDATE users SET money=money+? WHERE id=?");
        $stmt->execute([
            $money,
            $userId,
        ]);
        // 更新 SESSION 中的余额
        $_SESSION['money'] += $money;
    }
}