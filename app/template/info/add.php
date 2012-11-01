
<form name="form1" id="form1" method="POST" >
<table>

<tr><td>aaaa:</td><td><input type="text" id="aaaa" name="aaaa" value="<?=!empty($a['aaaa'])?trim($a['aaaa']):''; ?>" /></td></tr>
<tr><td>class_id:</td><td><input type="number" id="class_id" name="class_id" value="<?=!empty($a['class_id'])?$a['class_id']:''; ?>" /></td><td><span class="notice"></span></td></tr>

<tr><td></td><td><input type="submit" id="submit" value="提交" /><input type="reset" id="reset" value="重置" /></td></tr>
</table>
</form>

<script type="text/javascript">
<!--
$(document).ready(function(){
	$("#class_id").keyup(function(){
		var class_id = $("#class_id").val();
		var reg = /^[0-9]*$/;
		rs = reg.exec(class_id);
		if(rs == null) {
			$(".notice").html('请输入数字');
			} else {
			$(".notice").html('');
			}
		});
	$("#form1").submit(function(){
		var class_id = $("#class_id").val();
		var reg = /^[0-9]*$/;
		rs = reg.exec(class_id);
		if(rs == null) {
			alert('请按要求输入');
			return false;
			} 
		$.post("<?=empty($_GET['id']) ? url('/info/add') : url('/info/modify/id/').$a['id']?>",$("#form1").serialize(),function(result){
			if( ajaxHandler(result) )    return;
//			return false;   //因为没有回调，所以有没有都可以??
			});
		return false;   //防止页面post重复触发：
		});
});

//-->
</script>
