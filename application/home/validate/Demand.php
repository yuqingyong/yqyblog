<?php 
namespace app\home\validate;

use think\Validate;

class Demand extends Validate
{
    protected $rule =   [
        'title'  => 'require|max:100|token',
        'type'   => 'require',
        'content'=> 'require',
    ];
    
    protected $message  =   [
        'title.require'  => '标题必须',
        'type.require'   => '请选择需求类型',
        'content.require'=> '必须填写内容',
    ];
    
}





 ?>