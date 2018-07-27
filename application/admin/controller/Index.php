<?php
namespace app\admin\controller;
use app\common\controller\AdminBase;
use think\Cache;
/**
 * 后台首页控制器
 * @author yuqingyong
 */
class Index extends AdminBase
{
    public function index()
    {	
        return $this->fetch('Index/index');
    }

    /**
     * 网站基本信息展示
     */
    public function websit(){
		$info = array(
         '操作系统'=>PHP_OS,
         '运行环境'=>$_SERVER["SERVER_SOFTWARE"],
         'PHP运行方式'=>php_sapi_name(),
         'ThinkPHP版本'=>THINK_VERSION.' [ <a href="http://thinkphp.cn" target="_blank">查看最新版本</a> ]',
         '上传附件限制'=>ini_get('upload_max_filesize'),
         '执行时间限制'=>ini_get('max_execution_time').'秒',
         '服务器时间'=>date("Y年n月j日 H:i:s"),
         '北京时间'=>gmdate("Y年n月j日 H:i:s",time()+8*3600),
         '服务器域名/IP'=>$_SERVER['SERVER_NAME'].' [ '.gethostbyname($_SERVER['SERVER_NAME']).' ]',
         '剩余空间'=>round((disk_free_space(".")/(1024*1024)),2).'M',
         'register_globals'=>get_cfg_var("register_globals")=="1" ? "ON" : "OFF",
         'magic_quotes_gpc'=>(1===get_magic_quotes_gpc())?'YES':'NO',
         'magic_quotes_runtime'=>(1===get_magic_quotes_runtime())?'YES':'NO',
        );
		return $this->fetch('Index/websit',['info'=>$info]);
	}


	// 清除缓存
	public function clear_cache()
	{
		# 清除所有的缓存
		$res = Cache::clear();
		if($res == true){$this->success('清除缓存成功！');exit;}
	}



}
