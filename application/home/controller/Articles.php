<?php
namespace app\home\controller;
use app\common\controller\Homebase;
use app\admin\model\Article;
use app\admin\model\Comment;
use think\request;
use think\Controller;
use think\Db;
class Articles extends Homebase
{
	public function _empty()
	{
		$this->view->engine->layout(false);
		return view('Index/404');
	}
    //文章详情
    public function detail()
    {
    	//获取AID查询文章详情
    	$aid = input('aid');
    	$result = db('article')->where('aid',$aid)->find();
        //读取文章评论
        $comment  = new Comment();
        $comments = $comment->get_comment($aid);
    	//查询该文章对应的标签tid
    	$tid = db('article_tag')->where('aid',$aid)->field('tid')->select();
    	//查询相关的文章
    	$article = new Article();
    	$e_article = $article->getPageData('all',$tid[0]['tid'],'1','a.aid,title');
    	//dump($e_article);die;
    	$this->click($aid);
    	return view('Article/detail',
            ['art_detail'=>$result,
            'e_article'=>$e_article['list'],
            'comments'=>$comments
            ]);
    }

    //最新资讯
    public function news()
    {
    	//查询最新资讯cid=7
    	$article = new Article();
    	$n_article = $article->getPageData('7','all','1','title,a.aid,path,click,comment_num,create_time,description,a.cid,a.keywords');
    	//标签列表
    	$tags = db('tags')->order('tid desc')->select();
    	return view('Article/news',['list'=>$n_article['list'],'page'=>$n_article['page'],'tags'=>$tags]);
    }


    //技术笔记share
    public function jishu()
    {
        //查询技术笔记cid=2
        $article = new Article();
        $n_article = $article->getPageData('2','all','1','title,a.aid,path,click,comment_num,create_time,description,a.cid,a.keywords');
        //标签列表
        $tags = db('tags')->order('tid desc')->select();
        return view('Article/jishu',['list'=>$n_article['list'],'page'=>$n_article['page'],'tags'=>$tags]);
    }

    //源码分享
    public function share()
    {
        //查询源码分享cid=3
        $article = new Article();
        $n_article = $article->getPageData('3','all','1','title,a.aid,path,click,comment_num,create_time,description,a.cid,a.keywords');
        //标签列表
        $tags = db('tags')->order('tid desc')->select();
        return view('Article/share',['list'=>$n_article['list'],'page'=>$n_article['page'],'tags'=>$tags]);
    }

    //点击量增加
    public function click($aid)
    {
    	db('article')->where('aid',$aid)->field('click')->setInc('click');
    }

}
