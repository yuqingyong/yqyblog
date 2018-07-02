<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:76:"E:\phpStudy\WWW\yqyblog\public/../application/admin\view\User\user_list.html";i:1523149664;}*/ ?>
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
<form method="post" action="<?php echo url('admin/User/user_list'); ?>" id="listform">
   <div class="panel admin-panel">
    <div class="panel-head"><strong class="icon-reorder"> 会员列表</strong></div>
    <div class="padding border-bottom">
      <ul class="search" style="padding-left:10px;">
        <li>搜索：</li>
        <li>
          <input type="text" placeholder="请输入搜索关键字" name="word" class="input" style="width:250px; line-height:17px;display:inline-block" />
          <input type="submit" value="搜索" class="button border-main icon-search"/>
        </li>
      </ul>
    </div>
    <div class="panel-head"><strong class="icon-reorder"> 随言列表</strong></div>
    
    <table class="table table-hover text-center">
      <tr>
        <th width="100" style="text-align:left; padding-left:20px;">ID</th>
        <th>头像</th>
        <th>昵称</th>
        <th>注册类型</th>
        <th>最新登录</th>
        <th>登录次数</th>
        <th>是否启用</th>
        <th width="310">操作</th>
      </tr>
		<?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$va): $mod = ($i % 2 );++$i;?>
        <tr>
          <td style="text-align:left; padding-left:20px;"><input type="checkbox" name="id[]" value="" /><?php echo $va['uid']; ?></td>
          <td style="width:100px;height: 90px;"><img src="<?php echo $va['head_img']; ?>"></td>
          <td><?php echo $va['nickname']; ?></td>
          <td><?php echo $va['type']; ?></td>
          <td><?php echo date("Y-m-d H:i:s",$va['last_login_time']); ?></td>
          <td><?php echo $va['login_times']; ?></td>
		  <?php switch($va['status']): case "1": ?><td>是</td><?php break; case "0": ?><td style="color: red;">否</td><?php break; endswitch; ?>
          <td>
          	<div class="button-group"> 
          		<?php if($va['status'] == 0): ?>
          		<a class="button border-main" href="javascript:void(0)" onclick="return edit_status(<?php echo $va['uid']; ?>,1)"><span class="icon-edit"></span> 启用</a>
          		<?php else: ?>
          		<a class="button border-main" href="javascript:void(0)" onclick="return edit_status(<?php echo $va['uid']; ?>,0)"><span class="icon-edit"></span> 禁用</a>
          		<?php endif; ?>
          		<a class="button border-main" href="<?php echo url('admin/User/replay_email',array('uid'=>$va['uid'])); ?>"><span class="icon-edit"></span> 邮件回复</a>
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
//是否显示
function edit_status(uid,status)
{

	layer.confirm('您确定要修改状态?', {
	  btn: ['确定','取消'] //按钮
	}, function(){
	    $.ajax({
			type:"post",
			url:"<?php echo url('admin/User/edit_status'); ?>",
			data:{'uid':uid,'status':status},
			success:function(msg){
				var _json = JSON.parse(msg);
				if(_json.ok == 'y'){
					location.reload();
				}else{
					layer.msg('修改失败');
					location.reload();
				}
			}
		});
	}, function(){
	   layer.msg('已取消');
	});
	
	
}



//全选
$("#checkall").click(function(){ 
  $("input[name='id[]']").each(function(){
	  if (this.checked) {
		  this.checked = false;
	  }
	  else {
		  this.checked = true;
	  }
  });
})

//批量删除
function DelSelect(){
	var Checkbox=false;
	 $("input[name='id[]']").each(function(){
	  if (this.checked==true) {		
		Checkbox=true;	
	  }
	});
	if (Checkbox){
		var t=confirm("您确认要删除选中的内容吗？");
		if (t==false) return false;		
		$("#listform").submit();		
	}
	else{
		alert("请选择您要删除的内容!");
		return false;
	}
}

//批量排序
function sorts(){
	var Checkbox=false;
	 $("input[name='id[]']").each(function(){
	  if (this.checked==true) {		
		Checkbox=true;	
	  }
	});
	if (Checkbox){	
		
		$("#listform").submit();		
	}
	else{
		alert("请选择要操作的内容!");
		return false;
	}
}

</script>
</body>
</html>