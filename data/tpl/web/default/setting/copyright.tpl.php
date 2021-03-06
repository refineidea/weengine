<?php defined('IN_IA') or exit('Access Denied');?><?php include template('common/header', TEMPLATE_INCLUDEPATH);?>
	<div class="main">
		<form action="" method="post" class="form-horizontal form" enctype="multipart/form-data" onsubmit="return formcheck(this)">
			<h4>版权信息</h4>
			<table class="tb">
				<tr>
					<th><label for="">网站名称</label></th>
					<td>
						<input type="text" name="sitename" class="span6" value="<?php echo $settings['sitename'];?>" />
					</td>
				</tr>
				<tr>
					<th><label for="">网站URL</label></th>
					<td>
						<input type="text" name="url" class="span6" value="<?php echo $settings['url'];?>" />
					</td>
				</tr>
				<tr>
					<th><label for="">keywords</label></th>
					<td>
						<input type="text" name="keywords" class="span6" value="<?php echo $settings['keywords'];?>" />
					</td>
				</tr>
				<tr>
					<th><label for="">description</label></th>
					<td>
						<input type="text" name="description" class="span6" value="<?php echo $settings['description'];?>" />
					</td>
				</tr>
				<tr>
					<th><label for="">前台LOGO</label></th>
					<td>
						<div class="fileupload fileupload-new" data-provides="fileupload">
							<div class="fileupload-preview thumbnail" style="width: 200px; height: 150px;"><?php if($settings['flogo']) { ?><img src="<?php echo $_W['attachurl'];?><?php echo $settings['flogo'];?>" width="200" /><input type="hidden" name="flogo_old" value="<?php echo $settings['flogo'];?>" /><?php } ?></div>
							<div>
								<span class="btn btn-file"><span class="fileupload-new">选择图片</span><span class="fileupload-exists">更改</span><input name="flogo" type="file" /></span>
								<a href="#" class="btn fileupload-exists" data-dismiss="fileupload">移除</a>
								<?php if($settings['flogo']) { ?><button type="submit" name="fileupload-flogo-delete" value="<?php echo $settings['flogo'];?>" class="btn fileupload-new">删除</button><?php } ?>
							</div>
						</div>
						<span class="help-block"></span>
					</td>
				</tr>
				<tr>
					<th><label for="">后台LOGO</label></th>
					<td>
						<div class="fileupload fileupload-new" data-provides="fileupload">
							<div class="fileupload-preview thumbnail" style="width: 200px; height: 150px;"><?php if($settings['blogo']) { ?><img src="<?php echo $_W['attachurl'];?><?php echo $settings['blogo'];?>" width="200" /><input type="hidden" name="blogo_old" value="<?php echo $settings['blogo'];?>" /><?php } ?></div>
							<div>
								<span class="btn btn-file"><span class="fileupload-new">选择图片</span><span class="fileupload-exists">更改</span><input name="blogo" type="file" /></span>
								<a href="#" class="btn fileupload-exists" data-dismiss="fileupload">移除</a>
								<?php if($settings['blogo']) { ?><button type="submit" name="fileupload-blogo-delete" value="<?php echo $settings['blogo'];?>" class="btn fileupload-new">删除</button><?php } ?>
							</div>
						</div>
						<span class="help-block"></span>
					</td>
				</tr>
				<tr>
					<th>第三方统计代码</th>
					<td>
						<textarea style="height:100px;" class="span6" cols="70" name="statcode" autocomplete="off"><?php echo $settings['statcode'];?></textarea>
					</td>
				</tr>
				<tr>
					<th>底部左侧自定义</th>
					<td>
						<textarea style="height:150px;" class="span6" cols="70" name="footerleft" autocomplete="off"><?php echo $settings['footerleft'];?></textarea>
						<span class="help-block">自定义底部左侧信息，支持HTML</span>
					</td>
				</tr>
				<tr>
					<th>底部右侧自定义</th>
					<td>
						<textarea style="height:150px;" class="span6" cols="70" name="footerright" autocomplete="off"><?php echo $settings['footerright'];?></textarea>
						<span class="help-block">自定义底部右侧信息，支持HTML</span>
					</td>
				</tr>
				<tr>
					<th><label for="">联系电话</label></th>
					<td>
						<input type="text" name="phone" class="span6" value="<?php echo $settings['phone'];?>" />
					</td>
				</tr>
				<tr>
					<th><label for="">QQ</label></th>
					<td>
						<input type="text" name="qq" class="span6" value="<?php echo $settings['qq'];?>" />
					</td>
				</tr>
				<tr>
					<th><label for="">邮箱</label></th>
					<td>
						<input type="text" name="email" class="span6" value="<?php echo $settings['email'];?>" />
					</td>
				</tr>
				<tr>
					<th><label for="">详细地址</label></th>
					<td><div class="input-append"><input type="text" id="address" name="address" value="<?php echo $settings['address'];?>"  class="span5" /><button type="button" class="btn" name="submit" value="搜索" onclick="bmap.searchMapByAddress($('#address').val())">搜索</button></div><span class="help-block">可以通过查询地址，快速定位地图位置。</span></td>
				</tr>
				<tr>
					<th><label for="">地理位置：</label></th>
					<td><input type="text" name="lng" id="lng" value="<?php echo $settings['lng'];?>"  class="span3" /> - <input type="text" id="lat" name="lat" value="<?php echo $settings['lat'];?>"  class="span3" /></td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input name="submit" type="submit" value="提交" class="btn btn-primary span3" />
						<input type="hidden" name="token" value="<?php echo $_W['token'];?>" />
					</td>
				</tr>
				<tr>
					<th></th>
					<td><div id="baidumap" style="width:600px; height:500px;"></div></td>
				</tr>
				
			</table>
		</form>
	</div>
	<script type="text/javascript" src="http://api.map.baidu.com/api?v=1.4"></script>  
	<script type="text/javascript">
	var bmap = {
		'option' : {
			'lock' : false,
			'container' : 'baidumap',
			'infoWindow' : {'width' : 250, 'height' : 100, 'title' : ''},
			'point' : {'lng' : 116.403851, 'lat' : 39.915177}
		},
		'init' : function(option) {
			var $this = this;
			$this.option = $.extend({},$this.option,option);
			
			$this.option.defaultPoint = new BMap.Point($this.option.point.lng, $this.option.point.lat);
			$this.bgeo = new BMap.Geocoder();
			$this.bmap = new BMap.Map($this.option.container);
			$this.bmap.centerAndZoom($this.option.defaultPoint, 15);
			$this.bmap.enableScrollWheelZoom();
			$this.bmap.enableDragging();
			$this.bmap.enableContinuousZoom();
			$this.bmap.addControl(new BMap.NavigationControl());
			$this.bmap.addControl(new BMap.OverviewMapControl());
			//添加标注
			$this.marker = new BMap.Marker($this.option.defaultPoint);
			$this.marker.setLabel(new BMap.Label('请您移动此标记，选择您的坐标！', {'offset':new BMap.Size(10,-20)}));
			$this.marker.enableDragging();
			$this.bmap.addOverlay($this.marker);
			//$this.marker.setAnimation(BMAP_ANIMATION_BOUNCE);
			$this.showPointValue($this.marker.getPosition());
			//拖动地图事件
			$this.bmap.addEventListener("dragging", function() {
				$this.setMarkerCenter();
				$this.option.lock = false;
			});
			//缩入地图事件
			$this.bmap.addEventListener("zoomend", function() {
				$this.setMarkerCenter();
				$this.option.lock = false;
			});
			//拖动标记事件
			$this.marker.addEventListener("dragend", function (e) {
				$this.showPointValue();
				$this.showAddress();
				$this.bmap.panTo(new BMap.Point(e.point.lng, e.point.lat));
				$this.option.lock = false;
				$this.marker.setAnimation(null);
			});
		},
		'searchMapByAddress' : function(address) {
			var $this = this;
			 $this.bgeo.getPoint(address, function (point) {
				if (point) {
					$this.showPointValue();
					$this.showAddress();
					$this.bmap.panTo(point);
					$this.setMarkerCenter();
				}
			});
		},
		'searchMapByPCD' : function(address) {
			var $this = this;
			$this.option.lock = true;
			$this.searchMapByAddress($('#sel-provance').val()+$('#sel-city').val()+$('#sel-area').val());
		},
		'setMarkerCenter' : function() {
			var $this = this;
			var center = $this.bmap.getCenter();
			$this.marker.setPosition(new BMap.Point(center.lng, center.lat));
			$this.showPointValue();
			$this.showAddress();
		},
		'showPointValue' : function() {
			var $this = this;
			var point = $this.marker.getPosition();
			$('#lng').val(point.lng);
			$('#lat').val(point.lat);
		},
		'showAddress' : function() {
			var $this = this;
			var point = $this.marker.getPosition();
			$this.bgeo.getLocation(point, function (s) {
				if (s) {
					$('#address').val(s.address);
					if (!$this.option.lock) {
						cascdeInit(s.addressComponents.province,s.addressComponents.city,s.addressComponents.district);
					}
				}
			});
		}
	};
	$(function(){
		var option = {};
		<?php if(!empty($settings['lng']) && !empty($settings['lat'])) { ?>
		option = {'point' : {'lng' : '<?php echo $settings['lng'];?>', 'lat' : '<?php echo $settings['lat'];?>'}}
		<?php } ?>
		bmap.init(option);
	});
	</script>
<?php include template('common/footer', TEMPLATE_INCLUDEPATH);?>
