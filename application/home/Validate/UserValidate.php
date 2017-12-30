<?php 
namespace app\home\validate;
use think\Validate;
class UserValidate extends Validate
{
     protected $rule = [
        'username'  => 'require|max:25|unique:users',
        'password'  => 'require',
        'confirm_password'=>'require|confirm:password',
        'email' => 'require',
    ];
    
    protected $message = [
        'username.require' => '名称必须',
	    'username.max'     => '名称最多不能超过25个字符',
	    'username.unique'  => '名称已存在',
	    'confirm_password.confirm' => '两个密码不同',
	    'password'         => '密码必须填写',
	    'email'       	   => '邮箱必须',
    ];
    
   
    
}

