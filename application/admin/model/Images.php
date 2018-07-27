<?php
namespace app\admin\model;
use think\Db;
use think\Model;
/**	
 * 标签模型
 */
class Images extends Model{
	protected $table = 'yqy_advert';

	// 状态获取器
	public function getIsShowAttr($value)
    {
        $is_show = [0=>'禁用',1=>'正常'];
        return $is_show[$value];
    }





}