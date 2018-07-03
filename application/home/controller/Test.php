<?php
namespace app\home\controller;
use app\common\controller\HomeBase;
use app\common\model\CommentModel;
use app\api\model\SendSms;
use think\request;
use think\Db;
use think\Wechat;
class Test extends HomeBase
{
	//阿里大于短信测试
	public function alidayuSms()
	{
		$sms = new SendSms();
		$phone = "13372510395";
		//读取数据库配置
		$sms_config = Db::name('config')->where('type',3)->where('status',1)->field('config')->find();
		$config = json_decode($sms_config['config'],true);

		$res = $sms->sendSms($config['appid'],$config['appsecret'],$phone,$config['sign'],$config['smsid']);
		//根据返回值判断返回类型
		if($result->message && $result->message == 'OK'){
			return json_encode(['status'=>1,'msg'=>'发送成功']);
		}else{
			dump($result);die;
			return json_encode(['status'=>0,'msg'=>$result->message]);
		}
	}

	



	//微信发送模板消息测试
	public function sendtemplatemsg()
	{
		/*
         * data=>array(
                'first'=>array('value'=>urlencode("您好,您已购买成功"),'color'=>"#743A3A"),
                'name'=>array('value'=>urlencode("商品信息:微时代电影票"),'color'=>'#EEEEEE'),
                'remark'=>array('value'=>urlencode('永久有效!密码为:1231313'),'color'=>'#FFFFFF'),
            )
         */
		$touser = "ovXTHvzaY-q7qTq8aAJ8j2zeE1bk";
		$template_id = "ZBBRfN0JFHhuLmA8OmU8Ek9-P5ycABo0H8zagF0FTSY";
		$url = "http://www.yuqingyong.cn";
		$data = array(
			'username' => array('value'=>'小明','color'=>'#4876FF'),
			'date' => array('value'=>date('Y-m-d H:i:s'),'color'=>'#743A3A'),
			'businessname' => array('value'=>'江奕','color'=>'#743A3A'),
			'num' => array('value'=>'1','color'=>'#743A3A'),
			'couponname' => array('value'=>'20元优惠券','color'=>'#743A3A'),
		);

		$Sendwxmsg = new \think\weixin\Sendwxmsg($appid = "wxdd811d01f8afdbab",$secrect = "684df99dcd22963c9f54825a6a1948ef",$accessToken='');
		$res = $Sendwxmsg->doSend($touser, $template_id, $url, $data);
		dump($res);die;

	}

	//微信获取用户信息测试
	public function get_weixin_userinfo()
	{
		if(Cookie::has('openid')){
			$openid = Cookie::get('openid');
			//如果存在openid的缓存则不需要再次请求接口，说明user_info还没有过期，则查询是否存在
			$res = DB::table('yqy_weixin_user')->where('openid',$openid)->field('uid')->find();
			//如果存在则直接重定向首页，否则重定向至get_weixin_userinfo方法
			if($res){
				$this->redirect('/');	
			}else{
				$this->redirect(url('home/Test/get_weixin_userinfo'));
			}
		}else{
			//实例化WeChat类
			$weixin = new Wechat();

			//获取用户的基本信息
			$user_info = $weixin->_userInfoAuth('http://www.yuqingyong.cn/home/Test/get_weixin_userinfo');
			/**
			 * 获取到用户的openid,access_token之后，取出openid
			 * 判断用户的openid是否存在于数据库中
			 * 如果存在于数据库中，则直接session用户信息并跳转首页
			 * 否则将用户信息存储并跳转首页
			 */
			$this->is_openid($user_info['openid'],$user_info);
		}
	}



}