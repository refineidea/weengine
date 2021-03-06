<?php defined('IN_IA') or exit('Access Denied');?><input type="hidden" name="reply_id" value="<?php echo $reply['id'];?>" />
<div class="alert alert-block alert-new">
	<a class="close" data-dismiss="alert">×</a>
	<h4 class="alert-heading">添加砸蛋活动</h4>
	<table>
		<tbody>
			<tr>
				<th>查看内容</th>
				<td><a href="<?php echo $this->createWebUrl('awardlist', array('id' => $reply['rid']))?>" target="_blank">查看中奖名单</a></td>
			</tr>
			<tr>
				<th>活动图片</th>
				<td>
					<div id="" class="uneditable-input reply-edit-cover">
						<div class="detail">
							<span class="pull-right">大图片建议尺寸：700像素 * 300像素</span>
							<input type="button" id="egg-picture" fieldname="picture<?php echo $namesuffix;?>" class="btn btn-mini reply-edit-cover-upload" value="<i class='icon-upload-alt'></i> 上传" style="" />
							<button type="button" class="btn btn-mini reply-news-edit-cover-remove" id="upload-delete" onclick="doDeleteItemImage(this, 'egg-picture-value')" style="<?php if(empty($reply['picture'])) { ?> display:none;<?php } ?>"><i class="icon-remove"></i> 删除</button>
						</div>
						<?php if(!empty($reply['picture'])) { ?>
						<input type="hidden" name="picture-old" value="<?php echo $reply['picture'];?>">
						<div id="upload-file-view" class="upload-view">
							<input type="hidden" id="egg-picture-value" value="<?php echo $reply['picture'];?>">
							<img width="100" src="<?php echo $_W['attachurl'];?><?php echo $reply['picture'];?>">&nbsp;&nbsp;
						</div>
						<?php } else { ?>
						<div id="upload-file-view"></div>
						<?php } ?>
					</div>
				</td>
			</tr>
			<tr>
				<th>活动简介</th>
				<td>
					<textarea style="height:150px;" name="description" class="span7" cols="60"><?php echo $reply['description'];?></textarea>
					<div class="help-block">用于图文显示的简介</div>
				</td>
			</tr>
			<tr>
				<th>活动规则</th>
				<td>
					<textarea id="rule" style="height:150px;" name="rule" class="span7" cols="60"><?php echo $reply['rule'];?></textarea>
					<div class="help-block">活动的相关说明和活动奖品介绍。</div>
				</td>
			</tr>
			<tr>
				<th>未中奖提示</th>
				<td>
					<textarea style="height:150px;" name="default_tips" class="span7" cols="60"><?php echo $reply['default_tips'];?></textarea>
					<div class="help-block">当用户未中奖时，返回给用户的提示信息。</div>
				</td>
			</tr>
			<tr>
				<th>重复砸蛋周期</th>
				<td>
					<span class="uneditable-input span7">每
						<input type="text" value="<?php echo $reply['periodlottery'];?>" class="span1" name="periodlottery" placeholder="填天数">
						天，抽奖
						<input type="text" value="<?php echo $reply['maxlottery'];?>" class="span1" name="maxlottery" placeholder="填次数">
						次
					</span>
					<div class="help-block">天数为0，永远只能砸N次（这里N为设置的次数）。</div>
				</td>
			</tr>
			<tr>
				<th>中奖奖励积分</th>
				<td>
					<input type="text" value="<?php echo $reply['hitcredit'];?>" class="span7" name="hitcredit">
					<div class="help-block">当用户砸蛋砸中奖时，给予用户的积分。为0时表示不给。</div>
				</td>
			</tr>
			<tr>
				<th>未中奖奖励积分</th>
				<td>
					<input type="text" value="<?php echo $reply['misscredit'];?>" class="span7" name="misscredit">
					<div class="help-block">当用户砸蛋未中任何奖时，给予用户的积分。为0时表示不给。</div>
				</td>
			</tr>
		</tbody>
	</table>
	<div id="append-list" class="list">
	<?php if(!empty($award)) { ?>
		<?php $prize = 1;?>
		<?php if(is_array($award)) { foreach($award as $item) { ?>
		<div class="item" id="egg-item-<?php echo $item['id'];?>">
		<?php include $this->template('item');?>
		</div>
		<?php $prize++;?>
		<?php } } ?>
	<?php } ?>
	</div>
	<div class="reply-news-edit-button"><a href="javascript:;" onclick="eggHandler.buildAddForm('egg-form-html', $('#append-list'))" class="btn"><i class="icon-plus"></i> 添加奖品</a></div>
</div>
<script type="text/html" id="egg-form-html">
<?php unset($item); include $this->template('item');?>
</script>
<script type="text/javascript">
kindeditor($('#rule'));
kindeditorUploadBtn($('#egg-picture'));

var eggHandler = {
	'buildAddForm' : function(id, targetwrap) {
		var obj = buildAddForm(id, targetwrap);
		obj.html(obj.html().replace(/\(wrapitemid\)/gm, obj.attr('id')));
	}
};

function add_row() {
	$.getJSON('<?php echo create_url('site/module/formdisplay', array('name' => 'egg'))?>', function(data){
		if (data.error === 0 && data.content.html != '') {
			$('#append-list').append(data.content.html);
			row = $('#'+data.content.id);
		}
	});
}
//奖品类型切换
$("#append-list").delegate("#award-inkind input", "click", function(){
	if($(this).val() == 0) {
		$(this).parents(".item").find(".num").css("display", "none");
		$(this).parents(".item").find("tr:eq(3),tr:eq(4)").show();
	} else {
		$(this).parents(".item").find(".num").css("display", "inline-block");
		$(this).parents(".item").find("tr:eq(3),tr:eq(4)").hide();
	}
});
</script>
