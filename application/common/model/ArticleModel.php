<?php
namespace app\common\model;
use app\common\model\Base;
use think\Db;
use think\Cache;
/**	
 * 文章模型
 */
class ArticleModel extends Base{
	protected $table = "yqy_article";

	/**
     * 查询文章
     * @param   $cid   分类条件      
     * @param   $tid   标签ID      
     * @param   $is_show   是否显示      
     * @return  $data  返回数据
     */
	public function  getPageData($cid = 'all',$tid='all',$is_show='1',$field = '*',$is_delete=0,$limit=10)
	{
		if($cid == 'all' && $tid == 'all'){
			// 获取全部分类、全部标签下的文章
			if($is_show == 'all'){
				$where = ['is_delete'=>$is_delete];
			}else{
				$where = ['is_delete'=>$is_delete,'is_show'=>$is_show];
			}
			$list = Db::name('article')
				  ->alias('a')
				  ->join('yqy_article_pic b','a.aid = b.aid')
			      ->where($where)->order('a.sort desc')->field($field)->cache(true,3600*24)->paginate($limit);
		}elseif ($cid == 'all' && $tid != 'all') {
			//查询该标签下的所有文章
			if($is_show == 'all'){
				$where = ['a.is_delete'=>$is_delete,'at.tid'=>$tid];
			}else{
				$where = ['a.is_delete'=>$is_delete,'is_show'=>$is_show,'at.tid'=>$tid];
			}
			$list = Db::name('article_tag')
				  ->alias('at')
				  ->join('yqy_article a','at.aid = a.aid')
				  ->join('yqy_article_pic b','a.aid = b.aid')
				  ->where($where)->order('a.sort desc')->field($field)
				  ->cache(true,3600*24)->paginate($limit);	
		}elseif ($cid!='all' && $tid=='all') {
			//查询该分类下的所有文章
			if($is_show == 'all'){
				$where = ['is_delete'=>$is_delete,'cid'=>$cid];
			}else{
				$where = ['is_delete'=>$is_delete,'is_show'=>$is_show,'cid'=>$cid];
			}
			$list = Db::name('article')
			      ->alias('a')
				  ->join('yqy_article_pic b','a.aid = b.aid')
			      ->where($where)->order('a.sort desc')->field($field)->cache(true,3600*24)->paginate($limit);

		}
		//echo db('article')->getlastsql();die;
		$page = $list->render();
		$data = ['list'=>$list,'page'=>$page];
		return $data;

	}

	/**
     * 添加文章封面并推送     
     * @return  $resu  图片添加状态
     */
	public function add_article_pic()
	{
		$aid = $this->order('aid desc')->field('aid')->find();
        $info= getpic(input('post.content'));
        //生成缩略图
        $image = \think\Image::open('.'.$info);
        
		$image->thumb(220, 150)->save("./upload/".$aid['aid']."thumb.png");

		$p['path']= "/upload/".$aid['aid']."thumb.png";
		$p['aid'] = $aid['aid'];

      	//将得到的图片路径添加至图片表
      	$resu = Db::name('article_pic')->insert($p);
      	//百度推送文章
      	$urls = array();
      	$urls[] = "http://www.yuqingyong.cn/news_detail/".$aid['aid'];
      	bdUrls($urls);
      	if($resu){Cache::clear();return true;}else{return false;}
	}

	/**
     * 修改文章数据     
     * @return  $result  文章修改状态
     */
	public function edit_article($data)
	{
		$map    = ['aid'=>$data['aid']];
		$result = $this->editData($map,$data);
		//$result = Db::name('article')->where('aid',input('post.aid'))->update($data);
		if($result){Cache::clear();return true;}else{return false;}
	}

	/**
     * 根据传递的aid查询相关数据     
     * @return  $data  文章修改状态
     */
	public function getDataByAid($aid)
	{
		$data =  Db::name('article')
                 ->alias('a')
                 ->join('yqy_category b','a.cid = b.cid')
    	         ->where('aid',$aid)
    	         ->field('aid,title,a.cid,content,author,a.keywords,a.is_show,a.is_delete,sort,click,description,b.cname')->find();
    	return $data;
	}

	/**
     * 获取文章分类     
     * @return array $data  分类信息
     */
	public function getArticleCat()
	{
		$data = Db::name('category')->field('cname,cid')->select();
		return $data;
	}

	/**
     * 彻底删除文章     
     * @return true  返回状态
     */
	public function del_all($aid)
	{
    	//查询是否存在此文章
    	$article = Db::name('article')->where('aid',$aid)->field('aid')->find();
    	//查询图片路径
    	$path    = Db::name('article_pic')->where('aid',$aid)->field('path')->find();
    	if($article)
    	{
    		//如果存在则删除该条数据，并且删除图片的源文件
    		$res = Db::name('article')->where('aid',$aid)->delete();
    		Db::name('article_pic')->where('aid',$aid)->delete();
    		Db::name('article_tag')->where('aid',$aid)->delete();
    		unlink('.'.$path['path']);
    		Cache::clear();
    		return true;
    	}
	}

	/**
     * 修改文章的字段状态     
     * @return $res  返回状态
     */
	public function edit_ziduan($aid,$ziduan)
	{
		$res = Db::name('article')->where('aid',$aid)->update($ziduan);
		Cache::clear();
		return $res;
	}

	/**
     * 添加文章标签     
     * @return   返回状态
     */
	public function add_article_tag($data){
		//首先判断标签库中是否存在该文章的标签
        $a_tag = Db::name('article_tag')->where('aid',$data['aid'])->field('aid,tid')->select();
        if(!empty($a_tag)){
            //如果存在标签，则执行替换标签操作（将原有标签删除，增加新的标签）
            Db::name('article_tag')->where('aid',$data['aid'])->delete();
            foreach ($data['tid'] as $k => $v) {
                Db::name('article_tag')->insert(['aid'=>$data['aid'],'tid'=>$v]);
            }
            return true;
        }else{
            //如果不存在，则新增文章标签
            foreach ($data['tid'] as $k => $v) {
                Db::name('article_tag')->insert(['aid'=>$data['aid'],'tid'=>$v]);
            }
            return true;
        }
	}




}