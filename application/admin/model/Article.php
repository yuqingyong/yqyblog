<?php
namespace app\admin\model;
use think\db;
use think\Model;
/**	
 * 文章模型
 */
class Article extends Model{
	//获取文章列表数据
	public function  getPageData($cid = 'all',$tid='all',$is_show='1',$field = '*',$is_delete=0,$limit=10)
	{
		if($cid == 'all' && $tid == 'all'){
			// 获取全部分类、全部标签下的文章
			if($is_show == 'all'){
				$where = ['is_delete'=>$is_delete];
			}else{
				$where = ['is_delete'=>$is_delete,'is_show'=>$is_show];
			}
			$list = db('article')
				  ->alias('a')
				  ->join('yqy_article_pic b','a.aid = b.aid')
			      ->where($where)->order('a.sort desc')->field($field)->cache(true,60)->paginate($limit);
		}elseif ($cid == 'all' && $tid != 'all') {
			//查询该标签下的所有文章
			if($is_show == 'all'){
				$where = ['a.is_delete'=>$is_delete,'at.tid'=>$tid];
			}else{
				$where = ['a.is_delete'=>$is_delete,'is_show'=>$is_show,'at.tid'=>$tid];
			}
			$list = db('article_tag')
				  ->alias('at')
				  ->join('yqy_article a','at.aid = a.aid')
				  ->join('yqy_article_pic b','a.aid = b.aid')
				  ->where($where)->order('a.sort desc')->field($field)
				  ->cache(true,60)->paginate($limit);	
		}elseif ($cid!='all' && $tid=='all') {
			//查询该分类下的所有文章
			if($is_show == 'all'){
				$where = ['is_delete'=>$is_delete,'cid'=>$cid];
			}else{
				$where = ['is_delete'=>$is_delete,'is_show'=>$is_show,'cid'=>$cid];
			}
			$list = db('article')
			      ->alias('a')
				  ->join('yqy_article_pic b','a.aid = b.aid')
			      ->where($where)->order('a.sort desc')->field($field)->cache(true,60)->paginate($limit);

		}
		$page = $list->render();
		$data = ['list'=>$list,'page'=>$page];
		return $data;

	}





}