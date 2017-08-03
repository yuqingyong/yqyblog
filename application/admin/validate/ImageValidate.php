<?php 
namespace app\admin\validate;
use think\Validate;
class ImageValidate extends Validate
{
     protected $rule = [
        'mname'    => 'require',
        'from'     => 'require',
        'url'      => 'require',
        'end_time' => 'require',
    ];
    
    protected $message = [
        'mname.require' => '名称必须',
        'end_time'      => '结束时间必须',
        'from'          => '来源必须',
        'url'           => '外链容必须',
    ];
    
   
    
}







