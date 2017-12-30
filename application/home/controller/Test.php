<?php
namespace app\home\controller;
use app\common\controller\HomeBase;
use think\request;
use think\Controller;
use think\DB;
use think\Cookie;
use think\Wechat;
class Test extends HomeBase
{

	//清除缓存操作
	public function clear_cache()
	{
		Cookie::delete('user_info');
		Cookie::delete('openid');
		echo "Is ok";die;
	}

	//crontab 定时任务测试
	public function add_tag()
	{
		$data['tname'] = "测试";
		$res = db('tags')->insert($data);
		if($res)
		{
			//如果添加成功，则写入日志文件
			$this->use_log();exit;
		}
		exit;
	}


	//拼接详细的消费信息(格式:您好！某某用户在2017-05-29 14:25时分在商店一使用了2张免单券)
	public function use_log(){
		$time = date('Y-m-d H:i:s',time());
		$str  = "在".$time."执行了添加标签操作";
		$res  = file_put_contents("/data/wwwroot/default/test_log.txt",$str."\r\n",FILE_APPEND);
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


	//判断是否存在openid用户并处理
	public function is_openid($openid,$user_info)
	{
		$res = DB::table('yqy_weixin_user')->where('openid',$openid)->field('uid')->find();
		if(!empty($res)){
			//如果查询存在数据,则Cookie缓存查询到的数据
			Cookie::set('user_info',$user_info,86400*7);
			//重定向至首页
			$this->redirect('/');
		}else{
			//否则获取用户信息并将新用户添加入数据库中
			if ($user_info) {
				//如果存在则添加
				$data['openid'] = $user_info['openid'];
				$data['create_time'] = time();
				$data['update_time'] = time();
				$ress = DB::table('yqy_weixin_user')->insert($data);
				//Cookie缓存获取到的数据
				Cookie::set('user_info',$user_info,86400*7);
				//重定向至首页
				$this->redirect('/');
			}
		}
	}





}