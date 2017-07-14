<?php
namespace app\home\controller;
use app\common\controller\Homebase;
use app\common\model\Users;
use app\admin\model\Article;
use think\request;
use think\Controller;
use think\Db;
use think\Session;
use think\Cookie;
class Index extends Homebase
{
	public function _empty()
	{
		$this->view->engine->layout(false);
		return view('Index/404');
	}

    public function index()
    {
    	//友情链接
    	$link = db('link')->where('is_show',1)->order('sort desc')->cache('link',60)->select();

    	//查询首页轮播图
    	$banner = db('advert')->where(['type'=>1,'is_show'=>1])->field('mid,mname,img,url')->cache('banner',60)->select();
    	
    	//读取最新发布的文章
		$article = new Article();
    	$res = $article->getPageData('all','all','1','title,a.aid,path,click,comment_num,create_time,description,a.cid,a.keywords');
    	
        return view('Index/index',[
        	'banner'=>$banner,
        	'list'=>$res['list'],
        	'page'=>$res['page'],
        	'link'=>$link
        ]);
    }

    //文章查询
    public function article_search(Request $request)
    {
    	//标签列表
    	$tags = db('tags')->order('tid desc')->select();
    	$tag  = input('tid');
    	$word = input('word','','htmlentities');
    	if(!empty($tag) && empty($word)){
    		//标签查询
    		$model = new Article();
    		$article= $model->getPageData('all',$tag,'1','title,a.aid,path,click,comment_num,create_time,description,a.cid,a.keywords');
    		return view('Index/article_search',['list'=>$article['list'],'word'=>$word,'tags'=>$tags]);
    	}else{
    		//关键词查询
    		$where['title'] = ['like',"%".$word."%"];
    		$article = db('article')
    			 ->alias('a')
    			 ->join('yqy_article_pic b','a.aid = b.aid')
    			 ->where($where)->order('a.aid desc')
    			 ->field('path,title,a.aid,cid,description,click,create_time,keywords,comment_num')->select();
    		return view('Index/article_search',['list'=>$article,'word'=>$word,'tags'=>$tags]);
    	}
    	
    	
    	
    }

	//注册会员
	public function register(Request $request)
	{
		$this->view->engine->layout(false);
		if($request->ispost())
		{
			if(!captcha_check(input('post.code'))){
				$this->error('验证码错误');exit;
			}else{
				$data['username'] = input('post.username');
				$data['password'] = md5(input('post.password'));
				$data['email'] 	  = input('post.email');
				$data['type']     = 1;
				$res = db('users')->insert($data);
				if($res){$this->success('注册成功，前往登录...','home/Index/login');exit;}
			}
			
		}
		return view('Index/register');
	}
	
	
	//会员登录
	public function login(Request $request)
	{
		$this->view->engine->layout(false); 
		if($request->ispost())
		{
			$remember = input('post.remember');
			$username = input('post.username');
			$password = md5(input('post.password'));

			if(!captcha_check(input('post.code'))){
				$this->error('验证码错误');exit;
			}else{
				//验证用户账号密码
				$user = new Users();
				$result  = $user->check_password($username,$password);
				if($result != false && $result['status'] == 1){
					//保存用户的信息
					Session::set('users',$result);
					if($remember == 1)
					{
						Cookie::set('username',$username,3600);
						Cookie::set('password',input('post.password'),3600);
						Cookie::set('remember',$remember,3600);
					}
					$this->success('登录成功，前往首页...','home/Index/index');exit;
				}elseif($result!=false && $result['status'] == 0){
					$this->error('登录失败，账号已被禁用');exit;
				}else{
					$this->error('登录失败，请确认账号密码是否正确');exit;
				}
			}
		}
		return view('Index/login');
	}
	
	//退出登录
	public function log_out()
	{
		Session::set('users',null);
        $this->success('退出成功、前往登录页面', url('home/Index/login'));exit;
	}


	//检测用户名是否重复
	public function check_username()
	{
		$username = input('post.username');
		$res = db('users')->where('username',$username)->field('uid')->find();
		if($res){echo json_encode(['ok'=>'n']); exit;}else{echo json_encode(['ok'=>'y']); exit;}
	}













}
