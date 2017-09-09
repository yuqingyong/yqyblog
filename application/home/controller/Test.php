<?php
namespace app\home\controller;
use app\common\controller\Homebase;
use think\request;
use think\Controller;
use think\Db;
class Test extends Homebase
{
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












}