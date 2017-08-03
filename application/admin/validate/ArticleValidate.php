<?php 
namespace app\admin\validate;
use think\Validate;
class ArticleValidate extends Validate
{
     protected $rule = [
        'title'  => 'require|max:25',
        'cid'    => 'require',
        'author' => 'require',
        'content'=> 'require',
        'sort'   => 'number',
        'click'  => 'number',
    ];
    
    protected $message = [
        'title.require' => '名称必须',
	    'title.max'     => '名称最多不能超过25个字符',
	    'cid'           => '分类必须',
	    'author'        => '作者必须',
	    'content'       => '内容必须',
	    'sort'          => '排序必须为数字',
	    'click'         => '点击量必须为数字',
    ];
    
   
    
}







