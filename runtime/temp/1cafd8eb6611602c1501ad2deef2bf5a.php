<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:80:"E:\phpStudy\WWW\yuqingyong\public/../application/admin\view\Message\message.html";i:1523157043;}*/ ?>
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
<body>
  <div class="panel admin-panel">
    <div class="panel-head"><strong class="icon-reorder"> 留言列表</strong></div>
    <table class="table table-hover text-center">
      <tr>
        <th width="100" style="text-align:left; padding-left:20px;">ID</th>
        <th>用户</th>
        <th>内容</th>
        <th width="310">操作</th>
      </tr>
		<?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$va): $mod = ($i % 2 );++$i;?>
        <tr>
          <td style="text-align:left; padding-left:20px;"><input type="checkbox" name="id[]" value="" /><?php echo $va['id']; ?></td>
          <td width="10%"><?php echo $va['nickname']; ?></td>
          <td><?php echo $va['content']; ?></td>
          <td>
          	<div class="button-group">
          	<?php if($va['is_show'] == 1): ?>
          		<a class="button border-main" title="编辑" href="javascript:void(0)" onclick="return is_show(<?php echo $va['id']; ?>,0)"><span class="icon-edit"></span>取消显示</a>
          	<?php else: ?>
          		<a class="button border-main" title="编辑" href="javascript:void(0)" onclick="return is_show(<?php echo $va['id']; ?>,1)"><span class="icon-edit"></span>启动显示</a>
          	<?php endif; ?>
          		<a class="button border-main" href="javascript:void(0)" onclick="return del(<?php echo $va['id']; ?>)"><span class="icon-edit"></span>删除</a>
          	</div>
          </td>
        </tr>
		<?php endforeach; endif; else: echo "" ;endif; ?>
    </table>
    <div class="page">
    	<?php echo $page; ?>
    </div>
  </div>
</body>
<script>

//删除
function del(id){
	//询问框
	layer.confirm('您确定要删除该笔记吗?', {
	  btn: ['确定','取消'] //按钮
	}, function(){
	    $.ajax({
			type:"post",
			url:"<?php echo url('admin/Message/del'); ?>",
			data:{'id':id},
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
function is_show(id,is_show)
{

	layer.confirm('您确定要修改状态?', {
	  btn: ['确定','取消'] //按钮
	}, function(){
	    $.ajax({
			type:"post",
			url:"<?php echo url('admin/Message/is_show'); ?>",
			data:{'id':id,'is_show':is_show},
			success:function(msg){
				var _json = JSON.parse(msg);
				if(_json.ok == 'y'){
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
</html>