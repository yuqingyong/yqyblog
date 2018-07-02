<?php 
header("Content-Type: text/html;charset=utf-8");

$redis = new Redis();
$redis->connect('127.0.0.1',6379);
//echo "Connect to server successfully";
//echo "Server is running".$redis->ping();
//字符串设置
//$redis->set('one_word','hello world');
//echo $redis->get('one_word');

//redis LIST(列表) 实例
//$redis->lpush('one_list','redis');
//$redis->lpush('one_list','Mongodb');
//$redis->lpush('one_list','Mysql');
//$arlist = $redis->lrange('one_list',0,5);
//echo 'Stored string in redis';
//print_r($arlist);

//Redis PHP Keys实例 获取所有实例
//$arList = $redis->keys("*");
//print_r($arList);

//setex set一个存储失效
//$redis->setex('str',30,'hello world');

//setnx/msetnx相当于add操作，不会覆盖已有值
//$redis->setnx('foo',12);//true
//$redis->setnx('foo',34);//false

//getset操作，set的变种，结果返回替换前的值
//$redis->getset('foo',56);

//incrbt/incr.decrby/decr对值的递增和递减
//$redis->incr('foo');默认加一
//$redis->incrby('foo',2);//根据第二个参数的值增加

//exists检测是否存在某值
//$res = $redis->exists('foo');//true

//$redis->del('foo');//删除键民为foo的键值对

//类型检测，字符串返回string,列表返回list，set表返回set/zset,hash表返回hash
//$redis->type('foo');//不存在，返回none
//$redis->set('str','hello world');
//$redis->type('str');

//append链接到已存在字符串
//$redis->append('str','_my pleasure');//返回累加后的字符串长度8，此进str为

//setrange 部分替换操作
//$redis->setrange('str',0,'abc');//返回3，参数2为0时等同于set操作
//$redis->setrange('str',2,'cd');//返回4，表示从第2个字符串后替换，这时‘str'为abcd

//部分获取操作
//$res1 = $redis->substr('str',0,2);//表示从第0个起，渠道第二个字符，共三个
//获取字符串长度
//$res2 = $redis->strlen('str');

//setbit/getbit位存储和获取
//$redis->setbit('binary',31,1);//表示在第31位存入1
//$res1 = $redis->getbit('binary',31);//返回1

//keys模糊查找功能，支持*号以及？号（匹配一个字符）
//$redis->set('fool',123);
//$redis->set('foo2',456);
//$res4 = $redis->keys('foo*');//返回foo1和foo2的array
//$res5 = $redis->keys('f?o?');//返回foo1和foo2的array
//$res6 = $redis->keys('foo2');//返回foo2的array

//randomkey随机返回一个key
//$res = $redis->randomkey();

//rename.renamenx 对key进行改名，所不同的是renamenx不允许改成已存在的key
//$redis->rename('str','str2');

//expire 设置key-value的时效性,ttl 获取剩余有效期,persist 重新设置为永久存储
//$redis->expire('foo', 1); //设置有效期为1秒
//$redis->ttl('foo'); //返回有效期值1s
//$redis->expire('foo'); //取消expire行为

//dbsize 返回redis当前数据库的记录总数
//$redis->dbsize();

/**
 * 队列操作
 */


//rpush/rpushx有序列表操作，从队列后插入元素
//lpush/lpushx和rpush/rpushx的区别是插入到队列的头部，同上，x的含义是只对已存在的key操作
//$res1 = $redis->rpush('fooList','bar1');//返回一个列表的长度1
//$res2 = $redis->lpush('fooList','bar0');//返回一个列表的长度2
//$res3 = $redis->rpushx('fooList','bar2');//返回3,rpushx只对已存在的队列做添加,否则返回0
//返回当前列表的长度
//$res4 = $redis->llen('fooList'); //3

//lrange返回队列中的一个区间元素
//$res1 = $redis->lrange('fooList',0,1);//返回数组包含第0个至第1个共2个元素
//$res2 = $redis->lrange('fooList',0,-1);//返回第0个至倒数第一个,相当于返回所有元素,注意redis中很多时候会用到负数,下同


//$redis->rpush('fooList','bar1');
//$redis->lpush('fooList','bar0');
//$redis->rpushx('fooList','bar2');

//lindex返回指定顺序位置的list元素
//$res = $redis->lindex('fooList',1);

//修改队列中指定位置的value
//$redis->lset('fooList',1,'123');

//lrem 删除队列中左起指定数量的字符
//$redis->lrem('fooList',1,'_'); //删除队列中左起(右起使用-1)1个字符'_'(若有)

//lpop/rpop 类似栈结构地弹出(并删除)最左或最右的一个元素
//$redis->lpop('fooList');
//$redis->rpop('fooList');

//ltrim 队列修改，保留左边起若干元素，其余删除
//$redis->ltrim('fooList',0,1);

//rpoplpush 从一个队列中pop出元素并push到另一个队列
//$redis->rpush('list1','ab0');
//$redis->rpush('list1','ab1');
//$redis->rpush('list2','ab2');
//$redis->rpush('list2','ab3');
//$redis->rpoplpush('list1','list2');//结果list1 =>array(‘ab0′),list2 =>array(‘ab1′,’ab2′,’ab3′)
//$redis->rpoplpush('list2','list2');//也适用于同一个队列,把最后一个元素移到头部list2 =>array(‘ab3′,’ab1′,’ab2′)


//linsert 在队列的中间指定元素前或后插入元素
//$redis->linsert('list2', 'before','ab1','123'); //表示在元素’ab1′之前插入’123′
//$redis->linsert('list2', 'after','ab1','456');   //表示在元素’ab1′之后插入’456′

//blpop/brpop 阻塞并等待一个列队不为空时，再pop出最左或最右的一个元素（这个功能在php以外可以说非常好用）
//brpoplpush 同样是阻塞并等待操作，结果同rpoplpush一样
//$redis->blpop('list3',10); //如果list3为空则一直等待,直到不为空时将第一元素弹出,10秒后超时

//$arlist1 = $redis->lrange('list1',0,5);
//$arlist2 = $redis->lrange('list2',0,5);

//print_r($arlist2);


/**
 * set操作
 */

//sadd增加元素，返回true，重复返回false
$redis->sadd('set1','ab');
//$redis->sadd('set1','cd');
$redis->sadd('set1','ef');

//srem移除指定元素
//$redis->srem('set1','cd');

//spop弹出首元素
//$redis->spop('set1');

//smove移动当前set表的指定元素到另一个set元素
$redis->sadd('set2','123');
$redis->smove('set1','set2','ab');





print_r($redis->scard('set1'));
print_r($redis->scard('set2'));






?>