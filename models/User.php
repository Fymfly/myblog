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
            return TRUE;
        }
        else{
            return FALSE;
        }
    }


    // 为用户增加金额
    public function addMoney($money, $userId) {
        $stmt = self::$pdo->prepare("UPDATE users SET money=money+? WHERE id=?");
        return $stmt->execute([
            $money,
            $userId,
        ]);

        // 更新 Redis
        $redis = \libs\Redis::getInstance();

        // 拼出 redis 中的键
        $key = 'user_money:'.$userId;

        // 增加余额
        $ret = $redis->incrby($key, $money);

        echo $ret;
    }

    // 获取余额
    public function getMoney() {
        $id = $_SESSION['id'];

        // 查询数据库
        $stmt = self::$pdo->prepare('SELECT money FROM users WHERE id = ?');
        $stmt->execute([$id]);
        $money = $stmt->fetch( PDO::FETCH_COLUMN );
        // 保存到 SESSION 中
        $_SESSION['money'] = $money;
        return $money;
    }


    // 测试事务
    public function trans() {

        // 要求：所有的SQL语句 必须都成功，或者都失败

        // 事务：让多条 是SQL 语句都成功或者失败
        // 如何使用事务

        // 开启事务
        self::$pdo->exec('start transaction');

        // 执行多个 SQL
        $ret1 = self::$pdo->exec("update users set email='abc@126.com' where id=2"); 
        $ret2 = self::$pdo->exec("update users set email='bcd@126.com',money='123' where id=3");
        

        // 只有都成功时才提交事务，否则回滚事务
        if($ret1 !== FALSE && $ret2 !== FALSE){
            echo '提交';
            self::$pdo->exec('commit');    // 提交事务
        } else { 
            echo '回滚';
            self::$pdo->exec('rollback');  // 回滚事务
        }

    }
}