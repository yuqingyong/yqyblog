<?php
namespace app\admin\model;
use think\db;
use think\Model;
/**	
 * 友情链接模型
 */
class Link extends Model{
	// 定义自动验证规则
    protected $_validate=array(
        array('lanem','require','链接名称必填'),
        array('url','require','链接必填'),
        array('sort','require','排序必填'),
    );
	//读取友情链接
	public function get_link_list()
	{
		$list = Link::where(true)->order('lid desc')->field('lanem,lid,is_show,sort,url')->paginate(10);
    	$page = $list->render();
    	$data = ['list'=>$list,'page'=>$page];
    	return $data;
	}





}