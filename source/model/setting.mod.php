<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 */
defined('IN_IA') or exit('Access Denied');

function setting_upgrade() {
	global $_W;
	$pars = array();
	$pars['host'] = $_SERVER['HTTP_HOST'];
	$pars['family'] = IMS_FAMILY;
	$pars['version'] = IMS_VERSION;
	$pars['release'] = IMS_RELEASE_DATE;
	$pars['server'] = $_SERVER['SERVER_NAME'];
	$pars['key'] = $_W['config']['site']['key'];
	$pars['client'] = '';
	$clients = array(
		'/setting.php',
		'/source/model/setting.mod.php',
		'/source/controller/setting/upgrade.ctrl.php',
		'/themes/web/default/setting/upgrade.html'
	);
	$string = '';
	foreach($clients as $cli) {
		$string .= md5_file(IA_ROOT . $cli);
	}
	$pars['client'] = md5($string);

	$dat = ihttp_post('http://www.we7.cc/api/upgrade.php', $pars);
	$ret = array();
	if($dat && $dat['content']) {
		$content = $dat['content'];
		$obj = @simplexml_load_string($content, 'SimpleXMLElement', LIBXML_NOCDATA);
		$ret['family'] = strval($obj->family);
		$ret['version'] = strval($obj->version);
		$ret['release'] = strval($obj->release);
		$ret['announcement'] = strval($obj->announcement);
		$ret['error'] = strval($obj->error);
		if(empty($ret['error'])) {
			if(!empty($_W['config']['site']['token'])) {
				$string = $content . $_W['config']['site']['token'];
				if($dat['headers']['hash'] != md5($string)) {
					message('数据校验错误, 你的网络可能被攻击. 请检查并保证安全后重试.');
				}
			}
		}
		if($obj->schemas) {
			$ret['schemas'] = array();
			foreach($obj->schemas->schema as $schema) {
				$attr = $schema->attributes();
				$v = strval($attr['version']);
				$r = strval($attr['release']);
				$s = strval($schema);
				if($s) {
					$s = base64_decode($s);
				}
				$ret['schemas'][] = array(
					'version' => $v,
					'release' => $r,
					'script' => $s,
				);
				$ret['version'] = $v;
				$ret['release'] = $r;
			}
		}
		if($obj->attachments) {
			$ret['attachments'] = array();
			foreach($obj->attachments->file as $file) {
				$attr = $file->attributes();
				$path = strval($attr['path']);
				$sum = strval($attr['checksum']);
				$entry = IA_ROOT . $path;
				if(!is_file($entry) || md5_file($entry) != $sum) {
					$ret['attachments'][] = $path;
				}
			}
		}
	}
	if(!empty($ret)) {
		if($ret['error']) {
			switch($ret['error']) {
				case 'error-warning':
					message('存在错误, 不能自动更新. 错误详情: 你使用的系统是由非法渠道传播的, 微擎已记录这个情况, 泄露此系统的商业用户将被停止商业服务, 微擎团队保留法律追究的权利. (如果属于误报, 请联系微擎团队)');
					break;
				default:
					message("存在错误, 不能自动更新. 错误详情: {$ret['error']}");
					break;
			}
		}
		$ret['message'] = $ret['announcement'];
		$ret['upgrade'] = false;
		if($ret['attachments'] || $ret['schemas']) {
			if($ret['message']) {
				$ret['message'] .= '<hr/>';
			}
			$ret['message'] .= "<strong>存在新版本: {$ret['version']} (Release {$ret['release']})</strong><br />系统发布更新了, 赶快更新你的系统来体验新功能吧. <br /><br /><a href=\"javascript:;\" onclick=\"$('#we7_tips').remove();window.frames['main'].location.href='" . create_url('setting/upgrade') . "';\">立即查看更新</a>";
			$ret['upgrade'] = true;
		}
		$upgrade = array();
		$upgrade['message'] = $ret['message'];
		$upgrade['upgrade'] = $ret['upgrade'];
		$upgrade['lastupdate'] = TIMESTAMP;
		cache_write('upgrade', iserializer($upgrade));
	}
	return $ret;
}

function setting_upgrade_download($archive) {
	global $_W;
	$pars = array();
	$pars['host'] = $_SERVER['HTTP_HOST'];
	$pars['family'] = IMS_FAMILY;
	$pars['version'] = IMS_VERSION;
	$pars['release'] = IMS_RELEASE_DATE;
	$pars['server'] = $_SERVER['SERVER_NAME'];
	$pars['key'] = $_W['config']['site']['key'];
	$pars['archive'] = base64_encode(json_encode($archive));
	$url = 'http://www.we7.cc/api/upgrade.php';
	$tmpfile = IA_ROOT . '/data/tmp.zip';
	$fp = fopen($tmpfile, 'w+');
	if(!$fp) {
		return false;
	}
	$ch = curl_init($url); 
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $pars);
	curl_setopt($ch, CURLOPT_FILE, $fp);
	if(!curl_exec($ch)) {
		return false;
	}
	curl_close($ch); 
	fseek($fp, 0);
	$sign = fgets($fp, 33);
	$upfile = IA_ROOT . '/data/upgrade.zip';
	$handle = fopen($upfile, 'w');
	while (!feof($fp)) {
		$buffer = fgets($fp);
		fwrite($handle, $buffer);
	}
	fclose($handle);
	fclose($fp);
	unlink($tmpfile);
	if(md5_file($upfile) == $sign) {
		return $upfile;
	}
	return false;
}

function setting_upgrade_version($family, $version, $release) {
	$verfile = IA_ROOT . '/source/version.inc.php';
	$verdat = <<<VER
<?php
/**
 * 版本号
 * 
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 */

defined('IN_IA') or exit('Access Denied');

define('IMS_FAMILY', '{$family}');
define('IMS_VERSION', '{$version}');
define('IMS_RELEASE_DATE', '{$release}');
VER;
	file_put_contents($verfile, $verdat);
}

function setting_save($data = '', $key = '') {
	if (empty($data) && empty($key)) {
		return FALSE;
	}
	if (is_array($data) && empty($key)) {
		foreach ($data as $key => $value) {
			$record[] = "('$key', '".iserializer($value)."')";
		}
		if ($record) {
			$return = pdo_query("REPLACE INTO ".tablename('settings')." (`key`, `value`) VALUES " . implode(',', $record));
		}
	} else {
		$record = array();
		$record['key'] = $key;
		$record['value'] = iserializer($data);
		$return = pdo_insert('settings', $record, TRUE);
	}
	cache_build_setting();
	return $return;
}

function setting_load($key = '') {
	if (empty($key)) {
		$settings = pdo_fetchall('SELECT * FROM ' . tablename('settings'), array(), 'key');
		
	} else {
		$key = is_array($key) ? $key : array($key);
		$settings = pdo_fetchall('SELECT * FROM ' . tablename('settings') . " WHERE `key` IN ('".implode("','", $key)."')", array(), 'key');
	}
	if(is_array($settings)) {
		foreach($settings as $k => &$v) {
			$settings[$k] = iunserializer($v['value']);
		}
	}
	return $settings;
}

function setting_module_convert($manifest) {
	$module = array(
		'name' => $manifest['application']['identifie'],
		'title' => $manifest['application']['name'],
		'version' => $manifest['application']['version'],
		'type' => $manifest['application']['type'],
		'ability' => $manifest['application']['ability'],
		'description' => $manifest['application']['description'],
		'author' => $manifest['application']['author'],
		'url' => $manifest['application']['url'],
		'settings'  => intval($manifest['application']['setting']),
		'subscribes' => iserializer(is_array($manifest['platform']['subscribes']) ? $manifest['platform']['subscribes'] : array()),
		'handles' => iserializer(is_array($manifest['platform']['handles']) ? $manifest['platform']['handles'] : array()),
		'isrulefields' => intval($manifest['platform']['isrulefields']),
		'cover' => $manifest['bindings']['cover'],
		'rule' => $manifest['bindings']['rule'],
		'menu' => $manifest['bindings']['menu'],
		'home' => $manifest['bindings']['home'],
		'profile' => $manifest['bindings']['profile'],
		'shortcut' => $manifest['bindings']['shortcut'],
		'issystem' => 0
	);
	return $module;
}

function setting_module_manifest($modulename) {
	$manifest = array();
	$filename = IA_ROOT . '/source/modules/' . $modulename . '/manifest.xml';
	if (!file_exists($filename)) {
		return array();
	}
	$dom = new DOMDocument();
	$dom->load($filename);
	if($dom->schemaValidateSource(setting_module_manifest_validate())) {
		// 0.51xml
		$root = $dom->getElementsByTagName('manifest')->item(0);
		$vcode = explode(',', $root->getAttribute('versionCode'));
		$manifest['versions'] = array();
		if(is_array($vcode)) {
			foreach($vcode as $v) {
				$v = trim($v);
				if(!empty($v)) {
					$manifest['versions'][] = $v;
				}
			}
		}
		$manifest['install'] = $root->getElementsByTagName('install')->item(0)->textContent;
		$manifest['uninstall'] = $root->getElementsByTagName('uninstall')->item(0)->textContent;
		$manifest['upgrade'] = $root->getElementsByTagName('upgrade')->item(0)->textContent;
		$application = $root->getElementsByTagName('application')->item(0);
		$manifest['application'] = array(
			'name' => trim($application->getElementsByTagName('name')->item(0)->textContent),
			'identifie' => trim($application->getElementsByTagName('identifie')->item(0)->textContent),
			'version' => trim($application->getElementsByTagName('version')->item(0)->textContent),
			'type' => trim($application->getElementsByTagName('type')->item(0)->textContent),
			'ability' => trim($application->getElementsByTagName('ability')->item(0)->textContent),
			'description' => trim($application->getElementsByTagName('description')->item(0)->textContent),
			'author' => trim($application->getElementsByTagName('author')->item(0)->textContent),
			'url' => trim($application->getElementsByTagName('url')->item(0)->textContent),
			'setting' => trim($application->getAttribute('setting')) == 'true',
		);
		$platform = $root->getElementsByTagName('platform')->item(0);
		if(!empty($platform)) {
			$manifest['platform'] = array(
				'subscribes' => array(),
				'handles' => array(),
				'isrulefields' => false,
			);
			$subscribes = $platform->getElementsByTagName('subscribes')->item(0);
			if(!empty($subscribes)) {
				$messages = $subscribes->getElementsByTagName('message');
				for($i = 0; $i < $messages->length; $i++) {
					$t = $messages->item($i)->getAttribute('type');
					if(!empty($t)) {
						$manifest['platform']['subscribes'][] = $t;
					}
				}
			}
			$handles = $platform->getElementsByTagName('handles')->item(0);
			if(!empty($handles)) {
				$messages = $handles->getElementsByTagName('message');
				for($i = 0; $i < $messages->length; $i++) {
					$t = $messages->item($i)->getAttribute('type');
					if(!empty($t)) {
						$manifest['platform']['handles'][] = $t;
					}
				}
			}
			$rule = $platform->getElementsByTagName('rule')->item(0);
			if(!empty($rule) && $rule->getAttribute('embed') == 'true') {
				$manifest['platform']['isrulefields'] = true;
			}
		}
		$bindings = $root->getElementsByTagName('bindings')->item(0);
		if(!empty($bindings)) {
			global $points;
			$ps = array_keys($points);
			$manifest['bindings'] = array();
			foreach($ps as $p) {
				$define = $bindings->getElementsByTagName($p)->item(0);
				$manifest['bindings'][$p] = _setting_module_manifest_entries($define);
			}
		}
	} else {
		$err = error_get_last();
		if($err['type'] == 2) {
			return $err['message'];
		}
	}
	return $manifest;
}

function _setting_module_manifest_entries($elm) {
	$ret = array();
	if(!empty($elm)) {
		$call = $elm->getAttribute('call');
		if(!empty($call)) {
			$ret[] = array('call' => $call);
		}
		$entries = $elm->getElementsByTagName('entry');
		for($i = 0; $i < $entries->length; $i++) {
			$entry = $entries->item($i);
			$row = array(
				'title' => $entry->getAttribute('title'),
				'do' => $entry->getAttribute('do'),
				'direct' => $entry->getAttribute('direct') == 'true',
				'state' => $entry->getAttribute('state')
			);
			if(!empty($row['title']) && !empty($row['do'])) {
				$ret[] = $row;
			}
		}
	}
	return $ret;
}

function setting_module_manifest_validate() {
	$xsd = <<<TPL
<?xml version="1.0" encoding="utf-8"?>
<xs:schema xmlns="http://www.we7.cc" targetNamespace="http://www.we7.cc" xmlns:xs="http://www.w3.org/2001/XMLSchema" elementFormDefault="qualified">
	<xs:element name="entry">
		<xs:complexType>
			<xs:attribute name="title" type="xs:string" />
			<xs:attribute name="do" type="xs:string" />
			<xs:attribute name="direct" type="xs:boolean" />
			<xs:attribute name="state" type="xs:string" />
		</xs:complexType>
	</xs:element>
	<xs:element name="message">
		<xs:complexType>
			<xs:attribute name="type" type="xs:string" />
		</xs:complexType>
	</xs:element>
	<xs:element name="manifest">
		<xs:complexType>
			<xs:all>
				<xs:element name="application" minOccurs="1" maxOccurs="1">
					<xs:complexType>
						<xs:all>
							<xs:element name="name" type="xs:string" minOccurs="1" maxOccurs="1" />
							<xs:element name="identifie" type="xs:string"  minOccurs="1" maxOccurs="1" />
							<xs:element name="version" type="xs:string"  minOccurs="1" maxOccurs="1" />
							<xs:element name="type" type="xs:string"  minOccurs="1" maxOccurs="1" />
							<xs:element name="ability" type="xs:string"  minOccurs="1" maxOccurs="1" />
							<xs:element name="description" type="xs:string"  minOccurs="1" maxOccurs="1" />
							<xs:element name="author" type="xs:string"  minOccurs="1" maxOccurs="1" />
							<xs:element name="url" type="xs:string"  minOccurs="1" maxOccurs="1" />
						</xs:all>
						<xs:attribute name="setting" type="xs:boolean" />
					</xs:complexType>
				</xs:element>
				<xs:element name="platform" minOccurs="0" maxOccurs="1">
					<xs:complexType>
						<xs:all>
							<xs:element name="subscribes" minOccurs="0" maxOccurs="1">
								<xs:complexType>
									<xs:sequence>
										<xs:element ref="message" minOccurs="0" maxOccurs="unbounded" />
									</xs:sequence>
								</xs:complexType>
							</xs:element>
							<xs:element name="handles" minOccurs="0" maxOccurs="1">
								<xs:complexType>
									<xs:sequence>
										<xs:element ref="message" minOccurs="0" maxOccurs="unbounded" />
									</xs:sequence>
								</xs:complexType>
							</xs:element>
							<xs:element name="rule" minOccurs="0" maxOccurs="1">
								<xs:complexType>
									<xs:attribute name="embed" type="xs:boolean" />
								</xs:complexType>
							</xs:element>
						</xs:all>
					</xs:complexType>
				</xs:element>
				<xs:element name="bindings" minOccurs="0" maxOccurs="1">
					<xs:complexType>
						<xs:all>
							<xs:element name="cover" minOccurs="0" maxOccurs="1">
								<xs:complexType>
									<xs:sequence>
										<xs:element ref="entry" minOccurs="0" maxOccurs="unbounded" />
									</xs:sequence>
									<xs:attribute name="call" type="xs:string" />
								</xs:complexType>
							</xs:element>
							<xs:element name="rule" minOccurs="0" maxOccurs="1">
								<xs:complexType>
									<xs:sequence>
										<xs:element ref="entry" minOccurs="0" maxOccurs="unbounded" />
									</xs:sequence>
									<xs:attribute name="call" type="xs:string" />
								</xs:complexType>
							</xs:element>
							<xs:element name="menu" minOccurs="0" maxOccurs="1">
								<xs:complexType>
									<xs:sequence>
										<xs:element ref="entry" minOccurs="0" maxOccurs="unbounded" />
									</xs:sequence>
									<xs:attribute name="call" type="xs:string" />
								</xs:complexType>
							</xs:element>
							<xs:element name="home" minOccurs="0" maxOccurs="1">
								<xs:complexType>
									<xs:sequence>
										<xs:element ref="entry" minOccurs="0" maxOccurs="unbounded" />
									</xs:sequence>
									<xs:attribute name="call" type="xs:string" />
								</xs:complexType>
							</xs:element>
							<xs:element name="profile" minOccurs="0" maxOccurs="1">
								<xs:complexType>
									<xs:sequence>
										<xs:element ref="entry" minOccurs="0" maxOccurs="unbounded" />
									</xs:sequence>
									<xs:attribute name="call" type="xs:string" />
								</xs:complexType>
							</xs:element>
							<xs:element name="shortcut" minOccurs="0" maxOccurs="1">
								<xs:complexType>
									<xs:sequence>
										<xs:element ref="entry" minOccurs="0" maxOccurs="unbounded" />
									</xs:sequence>
									<xs:attribute name="call" type="xs:string" />
								</xs:complexType>
							</xs:element>
						</xs:all>
					</xs:complexType>
				</xs:element>
				<xs:element name="install" type="xs:string" minOccurs="0" maxOccurs="1" />
				<xs:element name="uninstall" type="xs:string" minOccurs="0" maxOccurs="1" />
				<xs:element name="upgrade" type="xs:string" minOccurs="0" maxOccurs="1" />
			</xs:all>
			<xs:attribute name="versionCode" type="xs:string" />
		</xs:complexType>
	</xs:element>
</xs:schema>
TPL;
	return trim($xsd);
}
