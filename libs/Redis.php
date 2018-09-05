<?php
namespace libs;

// 三私一公

class Redis {

    private static $redis = null;
    private function __clone() {}
    private function __construct() {}

    // 获取 redis 对象
    public static function getInstance() {
        
        // 从配置文件中读取账号
        $config = config('redis');

        // 如果还没有 redis 对象，就生成一个
        // 就只有 第一次 才会连接
        if(self::$redis === null) {

            // 放到队列中
            $redis = new \Predis\Client($config);
        }

        // 直接返已有的 redis 对象
        return self::$redis;
    }

}