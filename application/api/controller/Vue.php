<?php 
namespace app\api\controller;
use think\Controller;
use think\Db;
use think\Cache;
use think\Request;
use app\common\model\Users;
use app\common\model\ArticleModel;
//如果需要设置允许所有域名发起的跨域请求，可以使用通配符 *
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT,DELETE');
/**
 * Vue测试请求数据接口
 */
class Vue extends Controller{
	// 获取网站基本信息
	public function getWebInfo()
	{
		$web['all_article_num'] = Db::name('article')->count();
    	$web['web_day'] = timediff(1493568000,time());
    	$web['banner'] = Db::name('advert')->where(['type'=>1,'is_show'=>1])->field('mid,mname,img,url')->select();
    	foreach ($web['banner'] as $key => $value) {
    		$web['banner'][$key]['img'] = 'http://www.yuqingyong.cn'.$value['img'];
    	}
    	$arr = [
    		'status' => 1,
    		'res' => $web
    	];

    	return json_encode($arr);
	}

    // 获取文章列表
    public function getArticleList(Request $request)
    {
        $cid = $this->request->param('cid');
        $tid = $this->request->param('tid');
        $article = new ArticleModel();
        $article_list = $article->getPageData($cid,$tid,'1','title,a.aid,path,click,comment_num,create_time,description,a.cid,a.keywords');
        if($article_list){
            $arr = [
                'status' => 1,
                'res' => $article_list
            ];
        }elseif ($article_list == false) {
            $arr = [
                'status' => 0,
                'res' => '无相关数据'
            ];
        }else{
            $arr = [
                'status' => 0,
                'res' => '请求失败'
            ];
        }

        return json($arr);
    }

    // 查询文章
    public function searchArticle(Request $request)
    {
        $keyword = $this->request->param('keyword');
        if(is_numeric($keyword)){
            # 根据tid查询
            $where = ['a.is_delete'=>0,'is_show'=>1,'at.tid'=>(int)$keyword];
            
            $article = Db::name('article_tag')
                  ->alias('at')
                  ->join('yqy_article a','at.aid = a.aid')
                  ->join('yqy_article_pic b','a.aid = b.aid')
                  ->where($where)->order('a.sort desc')->field('path,title,a.aid,cid,description,click,create_time,keywords,comment_num')
                  ->select();

            # 查询keyword关键词
            $tag_name = Db::name('tags')->where('tid',$keyword)->field('tname')->find();
            $keyword = $tag_name['tname'];
        }else{
            # 根据关键词查询文章
            $where['title'] = ['like',"%".$keyword."%"];
            $article = Db::name('article')
                 ->alias('a')
                 ->join('yqy_article_pic b','a.aid = b.aid')
                 ->where($where)->order('a.aid desc')
                 ->field('path,title,a.aid,cid,description,click,create_time,keywords,comment_num')->select();
        }
        

        return json([
            'res' => $article,
            'status' => 1,
            'keyword'=>$keyword
        ]);
    }

    // 获取标签信息
    public function getTags()
    {
        $tags = Db::name('tags')->order('tid desc')->select();
        return json($tags);
    }

    // 读取最热文章
    public function getHotArticle()
    {
        //读取最热文章
        $hot_article = Db::name('article')
                     ->alias('a')
                     ->join('yqy_article_pic b','a.aid = b.aid')
                     ->where(['is_show'=>1])->order('click desc')->limit(8)
                     ->field('title,create_time,click,a.aid,path')->select();
        if($hot_article){
            $arr = [
                'status' => 1,
                'res' => $hot_article
            ];
        }else{
            $arr = [
                'status' => 0,
                'res' => '请求失败'
            ];
        }
        return json($arr);
    }

    // 获取文章详情
    public function getArticleDetail(Request $request)
    {
        $aid = $this->request->param('aid');
        $detail = Db::name('article')->where('aid',$aid)->find();
        if($detail){
            $arr = [
                'status' => 1,
                'res' => $detail
            ];
        }else{
            $arr = [
                'status' => 0,
                'res' => '请求失败'
            ];
        }

        return json($arr);
    }

    // 获取文章对应的评论
    public function getComment(Request $request)
    {
        $aid = $this->request->param('aid');
        $list = Db::name('comment')
              ->alias('a')
              ->join('yqy_users b','a.uid = b.uid')
              ->where(['a.status'=>1,'aid'=>$aid])->field('content,date,a.email,a.status,b.username,b.head_img')->select();
        foreach ($list as $k => $v) {
            $list[$k]['date'] = date('Y-m-d H:i:s',$v['date']);
        }
        return json($list);
    }

}

