<?php 
/**
 * 设置中心
 * 
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 */
define('IN_SYS', true);
require 'source/bootstrap.inc.php';
checklogin();
//checkaccount();

$actions = array('profile', 'module', 'designer', 'common', 'updatecache', 'register', 'copyright', 'upgrade', 'template', 'style');
$action = in_array($_GPC['act'], $actions) ? $_GPC['act'] : 'module';

$controller = 'setting';
require router($controller, $action);

