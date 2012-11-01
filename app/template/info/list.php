<?php $title = '产品 列表'; ?>
<?php include tplheader() ;
?>
<div class="add"></div>
<div>
<table>
<tr><td><button type="button" id="button"  >批量删除</button></td><td><a id="add" href="#">添加产品</a></td></tr>
<tr><th></th><th style="width:25%">id:</th><th style="width:25%">aaaa:</th><th style="width:25%">class_id:</th><th style="width:25%">编辑</th></tr>
<?php 
$i = 1;
foreach ($a as $v) {
	$color = ($i%2 == 1) ? 'skyblue' : 'grey';
	echo '<tr style="background-color:'.$color.'">';
	echo '<td><input type="checkbox" value=1 class="checkbox" value="'.$v['id'].'" /></td>';
	foreach($v as $key=>$value) {
		echo '<td>'.$value.'</td>';		
	}
	echo '<td><a href="'.url('/info/modify/id/').$v['id'].'">修改</a>&nbsp';
	echo '<a class="del" href="javascript:del(\''.$v['id'].'\')">删除</a></td>';
	echo '</tr>';
	echo '<div id="'.$v['id'].'"></div>';	
	$i++;
}
?>
</table>
</div>
<script type="text/javascript">
<!--
function del(id) {
	if(confirm('是否确定删除？')) {
		$.post("<?=url('/info/delete/id/')?>"+id,{},function(result){
			if(ajaxHandler(result)) return;
			});
		}
}
$(document).ready(function(){
	$("#add").click(function(){
		$(".add").load("<?=url('/info/add') ?>");
	});
});

//$("#button").click(function() {
//    var delarr={};
//	var i=0;
//	$(".checkbox").each(function(){
//		if($(this).attr("checked") == true	) {
//			delarr[i] = $(this).attr("value");
//			i++;
//			}
//		});
//	for(i=0;;i++){
//		}
//	$.post("<?=url('/info/batchdelete')?>", , function(result){
//		if(ajaxHandler(result)) return;
//		});
//});
//-->
</script>
<?php include tplfooter() ;?>