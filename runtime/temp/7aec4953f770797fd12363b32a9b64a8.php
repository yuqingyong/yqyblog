<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:80:"E:\phpStudy\WWW\yuqingyong\public/../application/admin\view\Article\huishou.html";i:1516915804;}*/ ?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<meta name="renderer" content="webkit">
<title></title>
<link rel="stylesheet" href="__PUBLIC__/admin/css/pintuer.css">
<link rel="stylesheet" href="__PUBLIC__/admin/css/admin.css">
<script src="__PUBLIC__/admin/js/jquery.js"></script>
<script src="__PUBLIC__/admin/js/pintuer.js"></script>
<script src="__PUBLIC__/admin/js/layer.js"></script>
</head>
<style>
 .content{
 	width: 100%;
 	height: 100%;
 	padding-left: 10px;
 	padding-top: 10px;
 }
 .content li{
 	width: 80px;
 	height: 30px;
 	float: left;
 }
 .btn{
 	text-align: center;
 	width: 100%;
 	height: 50px;
 	float: left;
 } 
 .top{
 	width: 100%;
 	height: 30px;
 	font-family: "微软雅黑",
 	font-size:14px;
 	font-weight: bold;
 }
</style>
<body>
<form method="post" action="<?php echo url('admin/Article/a_search'); ?>" id="listform">
   <div class="panel admin-panel">
    <div class="panel-head"><strong class="icon-reorder"> 文章列表</strong></div>
    <div class="padding border-bottom">
      <ul class="search" style="padding-left:10px;">
        <li> <a class="button border-main icon-plus-square-o" href="<?php echo url('admin/Article/add_article'); ?>"> 添加文章</a> </li>
        <li>搜索：</li>
        <li>
          <input type="text" placeholder="请输入搜索关键字" name="keyword" class="input" style="width:250px; line-height:17px;display:inline-block" />
          <input type="submit" value="搜索" class="button border-main icon-search"/>
        </li>
      </ul>
    </div>
    <div class="panel-head"><strong class="icon-reorder"> 文章列表</strong></div>
    
    <table class="table table-hover text-center">
      <tr>
        <th width="100" style="text-align:left; padding-left:20px;">ID</th>
        <th>封面</th>
        <th>标题</th>
        <th>发布时间</th>
        <th>作者</th>
        <th>排序</th>
        <th>点击量</th>
        <th>是否展示</th>
        <th>操作</th>
      </tr>
		<?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$va): $mod = ($i % 2 );++$i;?>
        <tr>
          <td style="text-align:left; padding-left:20px;"><input type="checkbox" name="id[]" value="" /><?php echo $va['aid']; ?></td>
          <td><img src="<?php echo $va['path']; ?>" alt="" style="width:100px;height: 90px;"></td>
          <td><?php echo $va['title']; ?></td>
          <td><?php echo $va['create_time']; ?></td>
          <td><?php echo $va['author']; ?></td>
          <td><?php echo $va['sort']; ?></td>
          <td><?php echo $va['click']; ?></td>
          <?php switch($va['is_show']): case "1": ?><td onclick="is_set(<?php echo $va['aid']; ?>,'best',0)">是</td><?php break; case "0": ?><td style="color: red;" onclick="is_set(<?php echo $va['aid']; ?>,'best',1)">否</td><?php break; endswitch; ?>
          <td>
          	<div class="button-group"> 
          		<a class="button border-main" href="javascript:void(0)" onclick="return huifu(<?php echo $va['aid']; ?>)"><span class="icon-edit"></span> 恢复</a>
          		<a class="button border-red" href="javascript:void(0)" onclick="return c_del(<?php echo $va['aid']; ?>)"><span class="icon-trash-o"></span> 彻底删除</a> 
          	</div>
          </td>
        </tr>
		<?php endforeach; endif; else: echo "" ;endif; ?>
    </table>
    <div class="page">
    	<?php echo $page; ?>
    </div>
  </div>
</form>
<script type="text/javascript">
//删除
function c_del(aid){
	//询问框
	layer.confirm('您确定要彻底删除该文章吗?', {
	  btn: ['确定','取消'] //按钮
	}, function(){
	    $.ajax({
			type:"post",
			url:"<?php echo url('admin/Article/c_del'); ?>",
			data:{'aid':aid},
			success:function(msg){
				var _json = JSON.parse(msg);
				if(_json.ok == 'y'){
					layer.msg('已删除');
					location.reload();
				}else{
					alert('删除失败');location.reload();
				}
			}
		});
	}, function(){
	   layer.msg('已取消');
	});

}

//是否显示
function huifu(aid)
{

	layer.confirm('您确定要恢复文章?', {
	  btn: ['确定','取消'] //按钮
	}, function(){
	    $.ajax({
			type:"post",
			url:"<?php echo url('admin/Article/a_huifu'); ?>",
			data:{'aid':aid},
			success:function(msg){
				var _json = JSON.parse(msg);
				if(_json.ok == 'y'){
					location.reload();
				}else{
					alert('恢复失败');location.reload();
				}
			}
		});
	}, function(){
	   layer.msg('已取消');
	});
	
	
}

</script>
</body>
</html>