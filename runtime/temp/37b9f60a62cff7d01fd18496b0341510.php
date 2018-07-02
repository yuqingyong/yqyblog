<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:75:"E:\phpStudy\WWW\yqyblog\public/../application/admin\view\Config\config.html";i:1530511951;}*/ ?>
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
 	width: 100px;
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
<form id="listform">
   <div class="panel admin-panel">
    <div class="padding border-bottom">
      <ul class="search" style="padding-left:10px;">
        <li> <a class="button border-main icon-plus-square-o" href="<?php echo url('admin/Config/add_config'); ?>"> 添加配置</a> </li>
      </ul>
    </div>
    <table class="table table-hover text-center">
      <tr>
        <th width="100" style="text-align:left; padding-left:20px;">ID</th>
        <th>配置名</th>
        <th>配置参数</th>
        <th>是否展示</th>
        <th>操作</th>
      </tr>
		<?php if(is_array($config) || $config instanceof \think\Collection || $config instanceof \think\Paginator): $i = 0; $__LIST__ = $config;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$va): $mod = ($i % 2 );++$i;?>
        <tr>
          <td style="text-align:left; padding-left:20px;"><input type="checkbox" name="id[]" value="" /><?php echo $va['id']; ?></td>
          <td><?php echo $va['config_name']; ?></td>
          <td><?php echo $va['config']; ?></td>
          <?php switch($va['status']): case "1": ?><td onclick="is_show(<?php echo $va['id']; ?>,0)">是</td><?php break; case "0": ?><td style="color: red;" onclick="is_show(<?php echo $va['id']; ?>,1)">否</td><?php break; endswitch; ?>
          <td>
          	<div class="button-group">
          		<a class="button border-main" href="<?php echo url('admin/Config/edit_config',array('id'=>$va['id'])); ?>"><span class="icon-edit"></span> 修改</a>
          		<a class="button border-red" href="javascript:void(0)" onclick="return del(<?php echo $va['id']; ?>)"><span class="icon-trash-o"></span> 删除</a> 
          	</div>
          </td>
        </tr>
		<?php endforeach; endif; else: echo "" ;endif; ?>
    </table>
  </div>
</form>
<script type="text/javascript">
//删除
function del(id){
	//询问框
	layer.confirm('您确定要删除该笔记吗?', {
	  btn: ['确定','取消'] //按钮
	}, function(){
	    $.ajax({
			type:"post",
			url:"<?php echo url('admin/Config/del_config'); ?>",
			data:{'id':id},
			success:function(msg){
				var _json = JSON.parse(msg);
				if(_json.status == 1){
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
function is_show(id,is_show)
{
	layer.confirm('您确定要修改状态?', {
	  btn: ['确定','取消'] //按钮
	}, function(){
	    $.ajax({
			type:"post",
			url:"<?php echo url('admin/Config/is_show'); ?>",
			data:{'id':id,'is_show':is_show},
			success:function(msg){
				var _json = JSON.parse(msg);
				if(_json.status == 1){
					location.reload();
				}else{
					alert('设置失败');location.reload();
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