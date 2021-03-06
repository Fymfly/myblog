<?php
namespace controllers;

use Intervention\Image\ImageManagerStatic as Image;

class TestController{   

    // 排行榜
    public function test() {
        
        // 发表日志的分值
        $data =[
            '3' => 40,
            '6' => 5,
            '8' => 22,
        ];
        // 发表评论的分值
        $data1 =[
            '9' => 100,
            '45' => 5,
            '8' => 22,
        ];
        // 发表日志的分值
        $data2 =[
            '5' => 50,
            '6' => 5,
            '98' => 45,
        ];


        // 把第二个数组中的数据合并到第一个数组中
        foreach($data1 as $k => $v) {

            if( isset( $data[$k] ) ) 
                $data[$k] += $v;
            else 
                $data[$k] =$v; 
        }

        // 把第三个数组中的数据合并到第一个数组中
        foreach($data2 as $k => $v) {

            if( isset( $data[$k] ) ) 
                $data[$k] += $v;
            else 
                $data[$k] =$v; 
        }

        // 把合并之后的数据根据分值倒叙排列
        arsort( $data );

        // 截取前20个
        var_dump(array_splice($data, 0, 3));

        echo '<pre>';
        var_dump( $data );

    }

    // 水印图片
    public function testImage() {

        // 打开要处理的图片
        $image = Image::make(ROOT . 'public/uploads/big.png');

        // 加水印图片
        $image->insert(ROOT . 'public/uploads/water.png','center');

        // 保存图片
        $image->save(ROOT . 'public/uploads/big_water.png');

    }


    // 测试事务
    public function testTrans() {
        $model = new \models\User;
        $model->trans();
    }


    // 测试生成订单ID
    public function testSnowflake() {

        $flake = new \libs\Snowflake(1013);
        for($i=0;$i<10;$i++) {
            echo $flake->nextId() . '<br>';
        }
    }

    // 在线编辑器过滤
    public function testPurify() {

        // 测试字符串
        $content = "你懂 <a href=''></a> 的 <a href=''>小技巧</a> sdfjk<div>sjkdfsd</div>sdjfkl <script>console.log('abc');</script>";

        // 1. 生成配置对象
        $config = \HTMLPurifier_Config::createDefault();

        // 2. 配置
        // 设置编码
        $config->set('Core.Encoding', 'utf-8');
        $config->set('HTML.Doctype', 'HTML 4.01 Transitional');
        // 设置缓存目录
        $config->set('Cache.SerializerPath', ROOT.'cache');
        // 设置允许的 HTML 标签
        $config->set('HTML.Allowed', 'script,div,b,strong,i,em,a[href|title],ul,ol,ol[start],li,p[style],br,span[style],img[width|height|alt|src],*[style|class],pre,hr,code,h2,h3,h4,h5,h6,blockquote,del,table,thead,tbody,tr,th,td');
        // 设置允许的 CSS
        $config->set('CSS.AllowedProperties', 'font,font-size,font-weight,font-style,margin,width,height,font-family,text-decoration,padding-left,color,background-color,text-align');
        // 设置是否自动添加 P 标签
        $config->set('AutoFormat.AutoParagraph', TRUE);
        // 设置是否删除空标签
        $config->set('AutoFormat.RemoveEmpty', TRUE);

        // 3. 过滤
        // 创建对象
        $purifier = new \HTMLPurifier($config);
        // 过滤
        $clean_html = $purifier->purify($content);

        echo $clean_html;
    }

    public function register(){
        
        //发邮件
        $redis = \libs\Redis::getInstance();
        //注意队列的信息
        $data =[
            'email'=>'2944065419@qq.com',
            'title'=>'biaoti',
            'content'=>'neirong',
        ];

        //数组转为JSON
        $data = json_encode($data);
        $redis->lpush('email',$data);
        echo "注册成功";
    }

    public function email(){
        ini_set('default_socket_timeout',-1);
        echo "邮件已经启动。。。。等待中。。。";

        $redis = \libs\Redis::getInstance();
        while(true){
            $data = $redis->brpop('email',0);
            echo "开始发邮件";

        }
        
       
    }
    public function mail(){
        // 设置邮件服务器账号
        $transport = (new \Swift_SmtpTransport('smtp.126.com', 25))  // 邮件器服务IP地址和端口号
        ->setUsername('czxy_qz@126.com')       // 发邮件账号
        ->setPassword('12345678abcdefg');      // 授权码

        // 创建发邮件对象
        $mailer = new \Swift_Mailer($transport);

        // 创建邮件消息
        $message = new \Swift_Message();

        $message->setSubject('测试标题')   // 标题
                ->setFrom(['czxy_qz@126.com' => '全栈1班'])   // 发件人
                ->setTo(['2944065419@qq.com', '2944065419@qq.com' => 'fengyuemin'])   // 收件人
                ->setBody('Hello <a href="http://localhost:9999">点击激活</a> World ~', 'text/html');     // 邮件内容及邮件内容类型

        // 发送邮件
        $ret = $mailer->send($message);
        var_dump($ret);
    }

    public function testmail(){
        $mail = new \libs\Mail;
        $mail->send('测试mail类标题','测试mail类内容',['2944065419@qq.com','fym']);
    }
    public function testconfig(){
        $re = config('redis');
        $db = config('db');
        echo '<pre>';
        var_dump($re);
        var_dump($db);
    }
    public function testlog(){
       $log = new \libs\Log('email');
        $log->log('发表成功');

    }
}
