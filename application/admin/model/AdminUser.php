<?php
namespace app\admin\model;
use think\db;
use think\Model;
/**	
 * 管理员模型
 */
class AdminUser extends Model{
	//管理员账号验证
	public function check_login($data){
		$data['password'] = md5(md5($data['password']).'yqy');
		$resu = db('admin_user')->where(['username'=>$data['username'],'password'=>$data['password']])->field('status,id,username,password')->find();
		if($resu['status'] == 1){return $resu;}else{return false;}
	}
}