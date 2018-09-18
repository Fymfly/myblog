<?php
// namespace Controllers;

// class RedbagController {

//     // 初始化
//     public function init() {
        
//         $redis = \libs\Redis::getInstance();

//         // 初始化库存量
//         $redis->set('redbog-stock', 20);

//         // 初始化空的集合
//         $key = 'redbag_'.date('Ymd');
//         $redis->sadd($key, '-1');

//         // 设置过期时间
//         $redis->expire($key, 3900);

//     }


//     // 监听消息队列，当有新的数据时就生成订单
//     public function makeOrder() {
//         $redis = \libs\Redis::getInstance();
//         $model = new \models\Redbag;

//         // 设置 socket 永不超时
//         ini_set('default_socket_timeout', -1); 

//         echo "开始监听红包队列... \r\n";

//         // 循环监听一个列表
//         while(true) {
            
//             // 从队列中取数据，设置为永久不超时
//             $data = $redis->brpop('redbag_orders', 0);
//             /*
//             返回的数据是一个数组用户的ID：[用户ID]
//             */
//             // 处理数据
//             $userId = $data[1];
//             // 下订单
//             $model->create($userId);

//             echo "========有人抢了红包！\r\n";
//         }
//     }
}