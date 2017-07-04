<?php
namespace app\admin\model;
use think\db;
use think\Model;
use think\Validate;
/**	
 * 用户表模型
 */
class Users extends Validate{

	protected $rule = [
        'name'  =>  'require|max:25',
        'email' =>  'email',
    ];
    
    protected $message = [
        'name.require'  =>  '用户名必须',
        'email' =>  '邮箱格式错误',
    ];
    
    protected $scene = [
        'add'   =>  ['name','email'],
        'edit'  =>  ['email'],
    ];    
}