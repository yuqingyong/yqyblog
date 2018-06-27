<?php

namespace app\index\controller;

use app\common\controller\IndexBase;
use think\Request;
use think\Session;
use think\Db;
use think\Config;
use think\Wechat;

class Weixin extends IndexBase
{
    private static $access_token = '../application/index/controller/access_token.json';

    // 授权登录
    function login()
    {
        $weixin = new Wechat();
        //获取用户的基本信息
        $user_info = $weixin->_userInfoAuth('http://www.sy0s1z.cn/index/Wxlogin/weixin_login');

        $res = Db::name('users')->where('openid', $user_info['openid'])->field('uid')->find();
        if (!empty($res)) {
            //session缓存获取到的数据
            Session::set('uid', $res['uid']);
            $this->redirect('/');
        } else {
            //否则获取用户信息并将新用户添加入数据库中
            $data['openid'] = $user_info['openid'];
            $data['create_time'] = time();
            $data['username'] = $user_info['nickname'];
            $data['headimg'] = $user_info['headimgurl'];
            $uid = Db::name('users')->insertGetId($data);
            //session缓存获取到的数据
            Session::set('uid', $uid);
            $this->redirect('/');
        }
    }

    // 二维码地址
    function qrcode()
    {
        return 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . $this->ticket();
    }

    // 响应公众号消息
    function event()
    {
        // 修改本url及token验证
        // $this->checkSignature();
        $xml = file_get_contents('php://input');
        if (!empty($xml)) {
            // 禁止加载外部实体，防止xml注入攻击
            libxml_disable_entity_loader(true);
            $xmlObj = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
            // 事件消息处理
            if (trim($xmlObj->MsgType) === 'event') {
                file_put_contents('../application/index/controller/event.json', json_encode($xmlObj));
                $type = trim($xmlObj->Event);
                if ($type === 'subscribe' || $type === 'unsubscribe' || $type === 'CLICK') {
                    if (trim($xmlObj->ToUserName) === 'gh_cd832431463c') {
                        $openId = trim($xmlObj->FromUserName);
                        if ($openId) {
                            if ($type === 'subscribe') {
                                if (preg_match('/^[1-9]\d*$/', $uid)) {
                                    empty(Db::name('users')->where('uid', $uid)->field('uid, openid')->find()) || !empty(Db::name('users')->where('openid', $openId)->field('openid')->find()) || Db::name('recommend')->insert(['openid' => $openId, 'uid' => $uid]);
                                }
                            } else if ($type === 'unsubscribe') {
                                Db::name('recommend')->where('openid', $openId)->delete();
                            } else if ($type === 'CLICK') {
                                $eventKey = trim($xmlObj->EventKey);
                                if ($eventKey === 'getcode') {
                                    $url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=' . self::token();
                                    $data = '{
                                        "touser":"' . $openId . '",
                                        "msgtype":"news",
                                        "news":{
                                            "articles": [
                                             {
                                                 "picurl":"http://www.yuqingyong.cn/favicon.ico"
                                             }
                                             ]
                                        }
                                    }';
                                    self::curl($url, $data);
                                }
                            }
                        }
                    }
                }
            }
        }
        return '';
    }

    // 创建菜单
    function createMenu()
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=' . self::token();
        $data = '{
            "button": [{
                    "type": "click",
                    "name": "全民种树",
                    "url": "http://www.sy0s1z.cn/index/Wxlogin/weixin_login"
                },
                {
                    "type": "click",
                    "name": "游戏攻略",
                    "key": "http://www.sy0s1z.cn/index/Game/book"
                },
                {
                    "type": "click",
                    "name": "联系我们",
                    "key": "contact"
                }
            ]
        }';
        self::curl($url, $data);
    }

    // 获取菜单
    private function getMenu()
    {
        return file_get_contents('https://api.weixin.qq.com/cgi-bin/menu/get?access_token=' . self::token());
    }

    // 删除菜单
    private function delMenu()
    {
        return file_get_contents('https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=' . self::token());
    }

    // token
    private static function token()
    {
        if (is_file(self::$access_token)) {
            $data = json_decode(file_get_contents(self::$access_token));
        } else {
            $data = new \stdClass();
            $data->expire_time = 0;
        }
        if ($data->expire_time < time()) {
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . Config::get('weixin.appid') . '&secret=' . Config::get('weixin.appsecret');
            $res = json_decode(self::curl($url));
            $access_token = $res->access_token;
            if ($access_token) {
                $data->expire_time = time() + 7000;
                $data->access_token = $access_token;
                $fp = fopen(self::$access_token, 'w');
                fwrite($fp, json_encode($data));
                fclose($fp);
            }
        } else {
            $access_token = $data->access_token;
        }
        return $access_token;
    }

    // ticket
    private function ticket()
    {
        $uid = Session::get('uid');
        if (!$uid) {
            if (Request::instance()->isAjax()) {
                echo json_encode(['ok' => 'e', 'msg' => '登录信息失效，即将刷新页面']);
            } else {
                $this->redirect(url('Index/index'));
            }
            die;
        }
        $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=' . self::token();
        $data = [
            'action_name' => 'QR_LIMIT_SCENE',
            'action_info' => [
                'scene' => [
                    'scene_id' => $uid
                ]
            ]
        ];
        return json_decode(self::curl($url, json_encode($data)))->ticket;
    }

    // 消息接口验证
    private function checkSignature()
    {
        $timestamp = $_GET['timestamp'];
        $nonce = $_GET['nonce'];
        $token = Config::get('weixin.token');
        $tmpArr = [$token, $timestamp, $nonce];
        sort($tmpArr, SORT_STRING);
        $tmpStr = sha1(implode($tmpArr));
        $tmpStr == $_GET['signature'] && die($_GET['echostr']);
    }

    // 抓取数据
    private static function curl($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);


//        curl_setopt($ch, CURLOPT_HEADER, false);    //启用时会将头文件的信息作为数据流输出。
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  //将curl_exec()获取的信息以字符串返回，而不是直接输出。
//
//        if ($https) {
//            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  //FALSE 禁止 cURL 验证对等证书（peer's certificate）。
//            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);  //验证主机
//        }


        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
    /*
        public function _userInfoAuth($url)
        {
            // 1.用户同意授权，获取code

            //2.用户手动同意授权,同意之后,获取code
            //页面跳转至redirect_uri/?code=CODE&state=STATE
            //$code = $_GET['code'];
            $code = input('get.code');
            if (!isset($code)) {
                $snsapi_userInfo_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . Config::get('weixin.appid') . '&redirect_uri=' . urlencode($url) . '&response_type=code&scope=snsapi_userinfo&state=YQJ#wechat_redirect';
                header("Location:{$snsapi_userInfo_url}");
                exit;
            }

            //3.通过code换取网页授权access_token
            $curl = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . Config::get('weixin.appid') . '&secret=' . Config::get('weixin.appsecret') . '&code=' . $code . '&grant_type=authorization_code';
            $content = $this->_request($curl);
            $result = json_decode($content);
            $res = $this->object2array($result);

            //4.通过access_token和openid拉取用户信息
            $webAccess_token = $res['access_token'];
            $openid = $res['openid'];
            $userInfourl = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $webAccess_token . '&openid=' . $openid . '&lang=zh_CN ';

            $recontent = $this->_request($userInfourl);
            $userInfo = json_decode($recontent, true);
            return $userInfo;
        }


        function object2array($object)
        {
            $object = json_decode(json_encode($object), true);
            return $object;
        }

        //设置网络请求配置
        public function _request($curl, $https = true, $method = 'GET', $data = null)
        {
            // 创建一个新cURL资源
            $ch = curl_init();

            // 设置URL和相应的选项
            curl_setopt($ch, CURLOPT_URL, $curl);    //要访问的网站
            curl_setopt($ch, CURLOPT_HEADER, false);    //启用时会将头文件的信息作为数据流输出。
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  //将curl_exec()获取的信息以字符串返回，而不是直接输出。

            if ($https) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  //FALSE 禁止 cURL 验证对等证书（peer's certificate）。
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);  //验证主机
            }
            if ($method == 'POST') {
                curl_setopt($ch, CURLOPT_POST, true);  //发送 POST 请求
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);  //全部数据使用HTTP协议中的 "POST" 操作来发送。
            }


            // 抓取URL并把它传递给浏览器
            $content = curl_exec($ch);
            if ($content === false) {
                return "网络请求出错: " . curl_error($ch);
                exit();
            }
            //关闭cURL资源，并且释放系统资源
            curl_close($ch);

            return $content;
        }
    */
}