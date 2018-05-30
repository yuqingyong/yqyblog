<?php 
namespace app\api\model;

/**
* Redis链接操作类
*/
class Redis
{
	// 定义redis对象
	private static $obj;

	// redis地址
	private static $host = '127.0.0.1';
	// 端口号
	private static $port = 6379;
	// 密码
	private static $pwd = '123456';

	//-------------------------------
	// 创建Redis对象
	//-------------------------------
	private static function obj()
	{
		if(is_null(self::$obj)){
			self::$obj = new \Redis();
			self::$obj->connect(self::$host,self::$port);
			is_null(self::$pwd) || self::$obj->auth(self::$pwd);
		}
		return self::$obj;
	}

	//-------------------------------
	// redis增删改查
	//-------------------------------
	static function __callStatic($name, $args)
	{
		$redis = self::obj();
		if(method_exists($redis, $name)){
			return call_user_func_array([$redis,$name], $args);
		}
	}

	//-------------------------------
	// 关闭redis链接
	//-------------------------------
	static function close()
	{
		is_null(self::$obj) || self::obj()->close();
	}


}