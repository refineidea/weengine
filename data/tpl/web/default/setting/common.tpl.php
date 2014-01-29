<?php defined('IN_IA') or exit('Access Denied');?><?php include template('common/header', TEMPLATE_INCLUDEPATH);?>
	<div class="main">
		<form action="" method="post" class="form-horizontal form" onsubmit="return formcheck(this)">
			<h4>系统锁操作</h4>
			<table class="tb">
				<tr>
					<th>删除升级锁</th>
					<td>
						<input name="bae_delete_update" type="submit" value="删除" class="btn" />
						<div class="help-block">升级“微擎”系统时，需要先删除升级锁，确保升级正常进行。</div>
					</td>
				</tr>
				<tr>
					<th>删除安装锁</th>
					<td>
						<input name="bae_delete_install" type="submit" value="删除" class="btn" />
						<div class="help-block">重新安装“微擎”系统时，需要先删除安装锁。</div>
					</td>
				</tr>
			</table>
		</form>
	</div>
<?php include template('common/footer', TEMPLATE_INCLUDEPATH);?>
