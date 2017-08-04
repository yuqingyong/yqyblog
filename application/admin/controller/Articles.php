<?php
namespace app\admin\controller;
use app\common\controller\Adminbase;
use app\admin\model\Article;
use think\controller;
use think\Db;
use think\request;
class Articles extends Adminbase
{
	//文章列表
    public function article_list()
    {
    	//读取已有的标签
    	$tags = db('tags')->field('tid,tname')->select();
    	$article = new Article();
    	$res = $article->getPageData('all','all','all');
		return view('Article/article_list',['list'=>$res['list'],'page'=>$res['page'],'tags'=>$tags]);
    }

    //添加文章
    public function add_article(Request $request)
    {
    	//读取文章分类
    	$category = db('category')->field('cname,cid')->select();
    	if($request->ispost())
    	{
    		$data = input('post.');
    		$data['create_time'] = time();
    		$article = new Article();
    		$result = $article->validate('ArticleValidate')->save($data);
			if(false === $result){
			    // 验证失败 输出错误信息
			    $this->error($article->getError());exit;
			}else{
				//如果添加成功则获取文章的封面图
				$aid = db('article')->order('aid desc')->field('aid')->find();
		        $info= getpic(input('post.content'));
		        //生成缩略图
		        $image = \think\Image::open('.'.$info);
				$image->thumb(220, 150)->save("./upload/".$aid['aid']."thumb.png");
				$p['path']= "/upload/".$aid['aid']."thumb.png";
				$p['aid'] = $aid['aid'];
		      	//将得到的图片路径添加至图片表
		      	$resu = db('article_pic')->insert($p);
		      	if($resu){$this->success('添加成功','Articles/article_list');exit;}
			}
    	}

    	return view('Article/add_article',['category'=>$category]);
    }

    //修改文章
    public function edit_article(Request $request)
    {
    	//查询详细信息
    	$article = db('article')
                 ->alias('a')
                 ->join('yqy_category b','a.cid = b.cid')
    	         ->where('aid',input('aid'))
    	         ->field('aid,title,a.cid,content,author,a.keywords,a.is_show,a.is_delete,sort,click,description,b.cname')->find();
    	//读取文章分类
    	$category = db('category')->field('cname,cid')->select();
    	if($request->ispost())
    	{
    		$data = input('post.');
    		$article = new Article();
    		$result = $article->where('aid',input('post.aid'))->update($data);
	      	if($result){$this->success('修改成功','Articles/article_list');exit;}
    	}
    	return view('Article/edit_article',['category'=>$category,'info'=>$article]);
    }

    //设置文章的显示状态
    public function is_show()
    {
    	$is_show = input('post.is_show');
    	$res = Article::where('aid',input('post.aid'))->update(['is_show'=>$is_show]);
    	if($res){echo json_encode(['ok'=>'y']);exit;}
    	//if($res){return json_encode(['ok'=>'y']);}
    }

    //删除文章,设为已删除状态
    public function is_delete()
    {
    	$res = Article::where('aid',input('post.aid'))->update(['is_delete'=>1]);
    	 
    	if($res){echo json_encode(['ok'=>'y']);exit;}
    	//if($res){return json_encode(['ok'=>'y']);}
    }

    //添加标签
    public function add_tag()
    {
    	//获取到需要添加的标签
    	$data = input('post.');
    	//首先判断标签库中是否存在该文章的标签
    	$a_tag = db('article_tag')->where('aid',$data['aid'])->field('aid,tid')->select();
    	if(!empty($a_tag)){
    		//如果存在标签，则执行替换标签操作（将原有标签删除，增加新的标签）
    		db('article_tag')->where('aid',$data['aid'])->delete();
    		foreach ($data['tid'] as $k => $v) {
    			db('article_tag')->insert(['aid'=>$data['aid'],'tid'=>$v]);
    		}
    		$this->success('已替换标签成功');exit;
    	}else{
    		//如果不存在，则新增文章标签
    		foreach ($data['tid'] as $k => $v) {
    			db('article_tag')->insert(['aid'=>$data['aid'],'tid'=>$v]);
    		}
    		$this->success('已新增标签成功');exit;
    	}

    }


    //文章回收站
    public function huishou()
    {
    	//读取已经被标记为is_delete=1的文章
    	$article = new Article();
    	$res = $article->getPageData('all','all','all','title,a.aid,b.path,create_time,author,sort,click,is_show,cid',1);
    	return view('Article/huishou',['list'=>$res['list'],'page'=>$res['page']]);
    }

    //文章恢复
    public function a_huifu()
    {
    	$aid = input('post.aid');
    	$res = db('article')->where('aid',$aid)->update(['is_delete'=>0]);
    	if($res){echo json_encode(['ok'=>'y']);exit;}
    	//if($res){return json_encode(['ok'=>'y']);}
    }



    //彻底删除文章
    public function c_del()
    {
    	$aid = input('post.aid');
    	//查询是否存在此文章
    	$article = db('article')->where('aid',$aid)->field('aid')->find();
    	//查询图片路径
    	$path    = db('article_pic')->where('aid',$aid)->field('path')->find();
    	if($article)
    	{
    		//如果存在则删除该条数据，并且删除图片的源文件
    		$res = db('article')->where('aid',$aid)->delete();
    		db('article_pic')->where('aid',$aid)->delete();
    		db('article_tag')->where('aid',$aid)->delete();
    		unlink('.'.$path['path']);
    		echo json_encode(['ok'=>'y']);exit;
    	}
    	
    }


    //百度推送
    public function baidu_site($article_url)
    {
       $pushresult=pushToBaidu(array($article_url));
       $pobj=json_decode($pushresult);//将返回的Json字符串转换成php可操作的对象
       if($pobj.success && $pobj.success>=1)
       {
          $this->success('推送成功');
       }else{
          $this->success('推送失败');
       }
    }








}
