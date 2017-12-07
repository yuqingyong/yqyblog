<?php
namespace app\common\controller;
use think\Request;
use think\Session;
use think\Cookie;
use think\Controller;
use think\Wechat;
class Homebase extends controller{
	
	public function _initialize()
	{
		//统计网站信息
    	$web['all_article_num'] = db('article')->count();
    	$web['web_day'] = timediff(1493568000,time());
		//读取最热文章
    	$hot_article = db('article')
    				 ->alias('a')
    				 ->join('yqy_article_pic b','a.aid = b.aid')
    				 ->where(['is_show'=>1])->order('click desc')->limit(8)
    				 ->field('title,create_time,click,a.aid,path')->select();
    			 
    	$this->assign('hot_article',$hot_article);
    	$this->assign('web',$web);
	}

}