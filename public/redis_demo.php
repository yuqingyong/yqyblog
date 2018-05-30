<?php 
header("Content-Type: text/html;charset=utf-8");

$redis = new Redis();
$redis->connect('127.0.0.1',6379);

// 添加一个元素
// echo $redis->sadd('set', 'cat');echo '<br>';
// echo $redis->sadd('set', 'cat');echo '<br>';
// echo $redis->sadd('set', 'dog');echo '<br>';
// echo $redis->sadd('set', 'rabbit');echo '<br>';
// echo $redis->sadd('set', 'bear');echo '<br>';
// echo $redis->sadd('set', 'horse');echo '<br>';
$redis->spop('set');
// 查看集合中所有的元素
$set = $redis->smembers('set');
print_r($set);echo '<br>';
?>