<?php
/* 支付宝支付的控制器 */
namespace controllers;

use Yansongda\Pay\Pay;

class AlipayController
{

    // 买家账号：csenje4446@sandbox.com
    // http://fym.tunnel.echomod.cn/alipay/notify

    // 配置
    public $config = [
        'app_id' => '2016091700531310',
        // 支付宝公钥
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA4KZ/KoqIgADaEP89hrBa0yxhNvYi6put7YkuHC9B7Lsw+aY2dk9AHDLcxDdZJPliDhc24nJW3w8RpNK5E8QGw1kaIbTJrumWN/HBj64f5K7elOR9gJBgmizlrDVQU7E22L9r1WYFy36FY1sUCmz6U3G6GZq8HYwISXEMlGpcXNtUe7oRokP3i2IBdgM5w7ojAPdfFIFpbHuATuocrEWu+eCgzJ+lsKI43iyi8HqHu2g1erLvmbPwnYOX70O02CO/AV19yckV9G8YGb1CVbMB/rx5iK0gfiBul4lkRATQHYODsjknYxVOhfkg/jqIo5DylGcrj2SmlcMobpe84g8ppQIDAQAB',
        // 商户应用密钥
        'private_key' => 'MIIEogIBAAKCAQEAslKN4W9RpveOhfolx29RpyynCiN1lFBozsdwuGSIC4g6cvfWrON2/O1LsJFBcLyC64M/QKufdrxET1LheQuc+YjdnfpnL6kCtpe1TawIcaDYO0K5pDCm3DSH9SqGWg4pqWkJnz1NK4WhMK1hqh4+OmNTcOSoyqhn9Unfg68ihRqsqxJYo0xcmye5qQdHIF2d8ShHbSJmcFyU9Uzu8sNrJ56nbVI3yZdj3R4FIVowFEP5T++WnvpeVweyHihNTGn2IBFIXaPBRDtJXdq6wesmPHJct3nzwqlZWxOV+hK0DmmClo9mGaDvDhL4ZcXby1fou7oZEXCCm43G0Q9Ind3UjwIDAQABAoIBACYFDfUTu/ACdiPAms9zv5AKKC80MjyGDGKSCUl3Pb5ftD6Q8vd3pAX3Ph0OS0qTFsLJ//F05hH1wAa9oA8j17soYD/vhJKX0VlG7UP7Ou2nIpM3/caxDNKEbLxr7atDu8Q//eoyssJlwmFThjA0NIZUaRT327khwNB+iKZV7+E63HyqBcaMoIC570/SWobuRxU2GkBM3PjlDhWcs0oGvVqBNQoBoWo4ZeG3QnlDdTtDQLEcqXgOBBgNwp0S6Y/vLu3V4uFr5ptE1zJWANeEmxNmyTIHg/9DMgdO66DMhoNEQWRDPkKMeV6RNBsZVQKsvqIzKv5L+vgikBsNAOj7e8kCgYEA4Po6ICOv6g9uTwqQbyARtHP/4Ut22cXaweVoBlT9+7qroLd9bvSAyydti0p69ps2HtVvjMpFCMqkQZcyrMSWhi03c9h6HOyrNHx8Ah54HxFgq2hiU/wfm8976W/mkLsuMs6/M/sXtY2nokOkRR4Hocw3nWifn4NBfUcBM9XB0vUCgYEAyulkcSL9qHCZ4qBWz+qW9G+YykRZihsEJHf2L+1EBtg1csg/+SXQcYAnFTqYPVyvK8yJCq3JGZBilaYPGZlPEfrJjEZOobNhNTIIrq47cwrc/YckQ+GYPs8ytTXyn1zH/KX/0ihOV57lH1kMuAodOp/2nds3t9+aWU1tvHAWfvMCgYAduZICexScFUvaz6eDtzX/pK/zQXhDj7u2kKvs4j/oiaJxiqzdAxsdPGlh1QZoHNvKuSKS9IqofbW0INkGMLc+pSzFdp2zwqVgOu5bjVELsc0W+KS9OfunJ4PUtP8+siyJc/2ZTZy1VTEH5G4I383cV9IlTxSAC+SUO9Rx19VTHQKBgCPfMvSVXQakMXBRLEfBj0JTYE2R28qAkDDqTEmYxof3PSu3nyequbj3EPG91CA0/Hrfw/JxWrX8QpF2NAEwizwAfBUicNBBaBQBbmuDPdtOtlbTx2OAxGuGMc67ZNMrkedmaV175q2y14q9MXRvxU8R7IVntef5zc2v1JCVuERlAoGAVi7Ha1Lgx5Bll29jfhRvlJvLBxp4CXdxEspeLrDygeFi2oG0TIDqKD/QxUUA8UaPeyyFeF7HgqU6H0tqLgiR8ElPJviIyEhHGzZ79VWUa49Aj2Lvg35MrksDUr5M1x67VxPwgEzac1zBNGCdjgc+H3w62uZsg3qBsVmemvwQ18c=',
        
        // 通知地址
        'notify_url' => 'http://fym.tunnel.echomod.cn/alipay/notify',
        // 跳回地址
        'return_url' => 'http://fym.tunnel.echomod.cn/alipay/return',
        
        // 沙箱模式（可选）
        'mode' => 'dev',
    ];


    // 跳转到支付宝
    public function pay() {

        // 接收订单编号
        $sn = $_POST['sn'];

        // 取出订单信息
        $order = new \models\Order;
        // 根据订单编号取出订单信息
        $data = $order->findBySn($sn);

        // var_dump($data); die;

        // 如果订单还未支付就跳到支付宝
        if( $data['status'] == 0) {

            // 跳转到支付宝
            $alipay = Pay::alipay($this->config)->web([

                // 先在本地的数据库中生成一个订单（支付的金额、支付状态等信息、订单号）
                // 模拟一个假的订单
                'out_trade_no' => $sn,    // 本地订单ID
                'total_amount' => $data['money'],    // 支付金额（单位：元）
                'subject' => '智聊系统用户充值：'.$data['money'].'元', // 支付标题

            ]);

            $alipay->send();

        } else {

            die('订单状态不允许支付~');

        }
        
    }


    // 支付完成跳回
    public function return() {
        // 验证数据是否是支付宝发过来
        $data = Pay::alipay($this->config)->verify();

        echo '<h1>支付成功！</h1> <hr>';

        var_dump( $data->all() );

    }


    // 接收支付完成的通知
    public function notify()
    {   
        echo 1;
        // 生成支付类的对象
        $alipay = Pay::alipay($this->config);
        echo 2;
        // $loger = new libs\Log('alipay');
        try{
            echo 3;
            // 判断消息是否是支付宝发过来的，以及判断这个消息有没有被中途串改，如果被改了就抛出异常
            $data = $alipay->verify(); // 是的，验签就这么简单！
            // echo 1;

            // 判断支付状态
            if($data->trade_status == 'TRADE_SUCCESS' || $data->trade_status == 'TRADE_FINISHED')
            {
                // 更新订单状态
                $order = new \models\Order;
                // 获取订单信息
                $orderInfo = $order->findBySn($data->out_trade_no);
                
                // var_dump($orderInfo);die;
                // 如果订单的状态为未支付状态 ，说明是第一次收到消息，更新订单状态 
                if($orderInfo['status'] == 0)
                {
                    // 设置订单为已支付状态
                    $order->setPaid($data->out_trade_no);

                    // 更新用户余额
                    $user = new \models\User;
                    $user->addMoney($orderInfo['money'], $orderInfo['user_id']);
                    // $loger->log('支付成功');
                }
                
            }
        } catch (\Exception $e) {

            // $loger->log('非法请求');
            die('非法请求');
        }

        // 回应支付宝服务器（如何不回应，支付宝会一直重复给你通知）
        $alipay->success()->send();
    }

    

    // 退款
    public function refund()
    {
        // 生成唯一退款订单号（以后使用这个订单号，可以到支付宝中查看退款的流程）
        $refundNo = md5( rand(1,99999) . microtime() );

        try{
            $order = [
                'out_trade_no' => '1536500818',    // 退款的本地订单号
                'refund_amount' => 0.01,              // 退款金额，单位元
                'out_request_no' => $refundNo,     // 生成 的退款订单号
            ];

            // 退款
            $ret = Pay::alipay($this->config)->refund($order);

            if($ret->code == 10000)
            {
                echo '退款成功！';
            }
            else
            {
                echo '失败' ;
                var_dump($ret);
            }
        }
        catch(\Exception $e)
        {
            var_dump( $e->getMessage() );
        }
    }

}