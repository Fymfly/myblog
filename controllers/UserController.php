<?php
namespace controllers;

use models\User;
use models\Order;

class UserController{

    // 把图片块合并
    public function uploadbig() {

        $count = $_POST['count'];  // 总的数量
        $i = $_POST['i'];        // 当前是第几块
        $size = $_POST['size'];   // 每块大小
        $name = 'big_img_'.$_POST['img_name'];  // 所有分块的名字
        $img = $_FILES['img'];    // 图片

        // 把每一个分片保存到 服务器 中
        move_uploaded_file( $img['tmp_name'] , ROOT.'tmp/'.$i); 

        // 如果所有分片都上传成功，就合并所有文件为一个文件

        $redis = \libs\Redis::getInstance();
        // 每上传一张图片，就把 redis 中的 conn_id + 1
        $uploadedCount = $redis->incr($name);

        // 如果是最后一个分支就合并
        if($uploadedCount == $count) {

            // 以追加的方式创建并打开最终的大文件
            $fp = fopen(ROOT.'public/uploads/big/'.$name.'.png','a');
            // 循环所有的分片
            for($i=0;$i<$count;$i++) {
                // 读取第 i 号文件并写到大文件中
                fwrite($fp, file_get_contents(ROOT.'tmp/'.$i) );
                // 删除第 i 号临时文件
                unlink(ROOT.'tmp/'.$i);
            }

            // 关闭文件
            fclose($fp);

            // 从 redis 中删除这个文件对应的编号这个变量
            $redis->del($name);

        }

    }


    // 设置相册
    public function uploadall() {

        // 先创建目录
        $root = ROOT.'public/uploads/';
        // 今天日期
        $date = date('Ymd');    // 20180913
        // 如果没有这个目录就创建目录
        if(!is_dir($root . $date)) {

            // 创建目录（0777：有写的权限，（只对Linux 系统有效））
            mkdir($root . $date, 0777);
        }

        foreach($_FILES['images']['name'] as $k => $v) {
            // 生成唯一的名字
            $name = md5( time() . rand(1,9999) );   // 32 位字符串
            // strrchr：从最后某一个字符开始截取到最后
            $ext = strrchr($v, '.');
            // 补上扩展名
            $name = $name . $ext;
            // 根据 name 的下标找到对应的临时文件并移动
            move_uploaded_file($_FILES['images']['tmp_name'][$k], $root . $date . '/' . $name);

            echo $root, $date . '/' . $name . '<hr>';
        }
    }

    // 显示相册视图
    public function album() {
        view('users.album');
    }

    // 设置头像
    public function setavatar() {
        // echo '<pre>';
        // var_dump( $_FILES );

        // 先创建目录
        $root = ROOT.'public/uploads/';
        // 今天日期
        $date = date('Ymd');    // 20180913
        // 如果没有这个目录就创建目录
        if(!is_dir($root . $date)) {

            // 创建目录（0777：有写的权限，（只对Linux 系统有效））
            mkdir($root . $date, 0777);
        }

        // 生成唯一的名字
        $name = md5( time() . rand(1,9999) );   // 32 位字符串

        // 补上文件的后缀
        // strrchr：从最后某一个字符开始截取到最后
        $ext = strrchr($_FILES['avatar']['name'],'.');

        // 补上扩展名
        $name = $name . $ext;

        // 移动图片
        move_uploaded_file($_FILES['avatar']['tmp_name'], $root . $date . '/' . $name);

        echo $root, $date.'/'.$name;
    }


    // 显示设置头像的视图
    public function avatar() {
        view('users.avatar');
    }

    public function test() {
        sleep();
    }

    // 查询订单状态的接口
    public function orderStatus() {
        
        $sn = $_GET['sn'];
        // 获取的次数
        $try = 10;
        $model = new Order;
        do
        {
            $info = $model->findBySn($sn);
            if($info['status'] == 0)
            {
                sleep(1);
                $try--;
            }
            else
                break;
        }while($try>0);
        echo $info['status'];
    }

    // 更新余额
    public function money() {
        $user = new User;
        echo $user->getMoney();
    }

    // 生成充值订单
    public function docharge() {

        // 生成订单
        $money = $_POST['money'];
        $model = new Order;
        $model->create($money);
        message('充值订单已生成，请立即支付', 2 ,'/user/orders');
    }

    // 订单列表
    public function orders() {

        $order = new Order;
        // 搜索数据
        $data = $order->search();

        view('users.order',$data);
    }

    // 显示充值视图
    public function charge() {
        view('users.charge');
    }

    public function hello(){

        $user = new User;
        $name = $user->getName();

        return view('users.hello',[
            'name'=>$name,
        ]);
        
    }

    public function world(){

        echo "helloworld";
    }


    public function register(){
        view('users.add');
    }

    public function store(){
        //1.接收表单
        $email = $_POST['email'];
        $pass = md5($_POST['password']);
        
        //2.生成激活码（随机的字符串）
        $code = md5( rand(1,99999) );
       
        //3.保存到redis
        $redis = \libs\Redis::getInstance();
         //序列化(数组转为 JSON 字符串)
         $value = json_encode([
            'email' => $email,
            'password' => $pass,
        ]);
        //键名
        $key = "temp_user:{$code}";
                        //设置过期时间
        $redis->setex($key, 300, $value);

        //4..把消息放到队列中
        $name = explode('@', $email);
        $from = [$email, $name[0]];
        $message = [
            'title'=> '账号激活',
            'content'=> "点击以下链接进行激活：<br> 点击激活：
            <a href='http://localhost:9999/user/active_user?code={$code}'>
            http://localhost:9999/user/active_user?code={$code}</a><p>
            如果按钮不能点击，请复制上面链接地址，在浏览器中访问来激活账号！</p>",  
            'from'=> $from,
        ];
        //把消息转成字符串  json => 序列化
        $message = json_encode($message);
        
        //连接redis
        $redis = \libs\Redis::getInstance();
        $redis->lpush('email', $message);
         echo "OK";

    }
    
    public function active_user(){
        //1.接收激活码
        $code = $_GET['code'];
        //2.到redis中取出账号
        $redis = \libs\Redis::getInstance();
        //拼出名字
        $key = 'temp_user:'.$code;
        //取出数据
        $data = $redis->get($key);
        
        if($data){
            //从 redis中删除激活码
            $redis->del($key);
            //反序列化
            $data = json_decode($data,true);
            //插入数据库中
            $user = new \models\User;
            $user->add($data['email'], $data['password']);
            
        }else{
            die("激活码无效");
        }
       
    }
    
    public function login(){
            view('users.login');
    }

    public function dologin(){
        $email = $_POST['email'];
        $pass = md5($_POST['password']);
        //使用模型
        $user = new User;
        if($user->login($email,$pass)){
            message('登陆成功', 2, '/blog/index');
        }else{
            message('用户名或者密码错误',1,'/user/login');
        }
    }
    public function logout(){
        $_SESSION = [];
        message('退出成功',2,'/');
    }
}