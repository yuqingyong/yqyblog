<?php 
namespace app\admin\validate;

use think\Validate;

class Login extends Validate
{
     protected $rule = [
        'username'  =>  'require',
        'password'  =>  'require',
    ];
    
    protected $message = [
        'username.require'  =>  '用户名必须',
        'password' =>  '用户密码必须',
    ];

}
