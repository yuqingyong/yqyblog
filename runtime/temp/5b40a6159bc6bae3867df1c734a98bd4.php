<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:83:"E:\phpStudy\WWW\yuqingyong\public/../application/admin\view\Demand\demand_list.html";i:1516915804;}*/ ?>
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
<form method="post" action="<?php echo url('admin/Articles/a_search'); ?>" id="listform">
   <div class="panel admin-panel">
    <div class="panel-head"><strong class="icon-reorder"> 需求列表</strong></div>
    <div class="padding border-bottom">
      <ul class="search" style="padding-left:10px;">
        <li>搜索：</li>
        <li>
          <input type="text" placeholder="请输入搜索关键字" name="keyword" class="input" style="width:250px; line-height:17px;display:inline-block" />
          <input type="submit" value="搜索" class="button border-main icon-search"/>
        </li>
      </ul>
    </div>
    <div class="panel-head"><strong class="icon-reorder"> 需求列表</strong></div>
    
    <table class="table table-hover text-center">
      <tr>
        <th width="100" style="text-align:left; padding-left:20px;">ID</th>
        <th>标题</th>
        <th>发布时间</th>
        <th>发布者</th>
        <th>点击量</th>
        <th>是否展示</th>
        <th>操作</th>
      </tr>
		<?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$va): $mod = ($i % 2 );++$i;?>
        <tr>
          <td style="text-align:left; padding-left:20px;"><input type="checkbox" name="id[]" value="" /><?php echo $va['xid']; ?></td>
          <td><?php echo $va['title']; ?></td>
          <td><?php echo $va['create_time']; ?></td>
          <td><?php echo $va['user_name']; ?></td>
          <td><?php echo $va['see_num']; ?></td>
          <?php switch($va['is_show']): case "1": ?><td onclick="is_set(<?php echo $va['xid']; ?>,'best',0)">是</td><?php break; case "0": ?><td style="color: red;" onclick="is_set(<?php echo $va['xid']; ?>,'best',1)">否</td><?php break; endswitch; ?>
          <td>
          	<div class="button-group"> 
          		<?php if($va['is_show'] == 0): ?>
          		<a class="button border-main" href="javascript:void(0)" onclick="return is_show(<?php echo $va['xid']; ?>,1)"><span class="icon-edit"></span> 启用</a>
          		<?php else: ?>
          		<a class="button border-main" href="javascript:void(0)" onclick="return is_show(<?php echo $va['xid']; ?>,0)"><span class="icon-edit"></span> 禁用</a>
          		<?php endif; ?>
          		<a class="button border-red" href="javascript:void(0)" onclick="return del(<?php echo $va['xid']; ?>)"><span class="icon-trash-o"></span> 删除</a> 
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
function del(xid){
	//询问框
	layer.confirm('您确定要删除该需求吗?', {
	  btn: ['确定','取消'] //按钮
	}, function(){
	    $.ajax({
			type:"post",
			url:"<?php echo url('admin/Demands/delete'); ?>",
			data:{'xid':xid},
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
function is_show(xid,is_show)
{

	layer.confirm('您确定要修改状态?', {
	  btn: ['确定','取消'] //按钮
	}, function(){
	    $.ajax({
			type:"post",
			url:"<?php echo url('admin/Demands/is_show'); ?>",
			data:{'xid':xid,'is_show':is_show},
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
</body>
</html>