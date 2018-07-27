<?php
namespace app\home\controller;
use think\Controller;
use think\Session;
use think\request;
use think\Db;
class Regnotify extends Controller
{
    // 发起登录请求
    public function qqsend()
    {
        // 参数
        $url = "https://graph.qq.com/oauth2.0/authorize";
        $param['response_type'] = "code";
        $param['client_id']="101482622";
        $param['redirect_uri'] ="http://www.yuqingyong.cn/home/Regnotify/QqNotify";
        $param['scope'] ="get_user_info";
        //-------生成唯一随机串防CSRF攻击
        $param['state'] = md5(uniqid(rand(), TRUE));
        // $_SESSION['state'] = $param['state'];
        Session::set('state',$param['state']);
        //拼接url
        $param = http_build_query($param,"","&");
        $url = $url."?".$param;
        header("Location:".$url);exit;
    }

	// QQ互联回调地址
	public function QqNotify()
	{
		$code = input('get.code');
        $state = input('get.state');
        if($code && $state == Session::get('state')){
            //获取access_token
            $res = $this->getAccessToken($code,"101482622","989701c6a1ad6a3a8aef37500b55b384");
            parse_str($res,$data);
            $access_token = $data['access_token'];
            $url  = "https://graph.qq.com/oauth2.0/me?access_token=$access_token";
            $open_res = $this->httpsRequest($url);
            if(strpos($open_res,"callback") !== false){
                $lpos = strpos($open_res,"(");
                $rpos = strrpos($open_res,")");
                $open_res = substr($open_res,$lpos + 1 ,$rpos - $lpos - 1);
            }
            $user = json_decode($open_res);
            $open_id = $user->openid;
            $url = "https://graph.qq.com/user/get_user_info?access_token=$access_token&oauth_consumer_key=101482622&openid=$open_id";
            $user_info = $this->httpsRequest($url);
            # 查询是否已经存在该openid
            $res = Db::name('users')->where('openid',$open_id)->field('type,status,uid,username')->find();
            if($res){
                # 如果验证通过则更新用户的登录IP和时间
                $ta['last_login_time'] = time();
                $ta['last_login_ip']   = get_real_ip();
                Db::name('users')->where('uid',$res['uid'])->field('last_login_time,last_login_ip')->update($ta);
                # 登录次数自增1
                Db::name('users')->where('uid',$res['uid'])->setInc('login_times');
            	Session::set('users',$res);
                $this->redirect('/');
            }else{
                $user_info = json_decode($user_info,true);
                $da['type'] = 2;
                $da['openid']   = $open_id;
                $da['username'] = $user_info['nickname'];
                $da['password'] = md5('123456');
                $da['nickname'] = $user_info['nickname'];
                $da['head_img'] = $user_info['figureurl_qq_1'];
                $da['create_time'] = time();
                $uid = Db::name('users')->insertGetId($da);
                $users = Db::name('users')->where('uid',$uid)->field('username,type,status,uid')->find();
                Session::set('users',$users);
                $this->redirect('/');
            }

        }
	}

	// 通过Authorization Code获取Access Token
    public function getAccessToken($code,$app_id,$app_key){
        $url="https://graph.qq.com/oauth2.0/token";
        $param['grant_type']="authorization_code";
        $param['client_id']=$app_id;
        $param['client_secret']=$app_key;
        $param['code']=$code;
        $param['redirect_uri']="http://www.yuqingyong.cn/home/Regnotify/QqNotify";
        $param =http_build_query($param,"","&");
        $url=$url."?".$param;
        return $this->httpsRequest($url);
    }
    // httpsRequest
    public function httpsRequest($post_url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$post_url);//要访问的地址
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//执行结果是否被返回，0是返回，1是不返回
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);//设置超时
        $res = curl_exec($ch);//执行并获取数据
        return $res;
        curl_close($ch);
    }
}