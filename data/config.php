<?php
defined('IN_IA') or exit('Access Denied');

$config = array();

$config['db']['host'] = 'localhost';
$config['db']['username'] = 'root';
$config['db']['password'] = 'leo.lixiao';
$config['db']['port'] = '3306';
$config['db']['database'] = 'we7';
$config['db']['charset'] = 'utf8';
$config['db']['pconnect'] = 0;
$config['db']['tablepre'] = 'ims_';

// --------------------------  CONFIG COOKIE  --------------------------- //
$config['cookie']['pre'] = '8778_';
$config['cookie']['domain'] = '';
$config['cookie']['path'] = '/';

// --------------------------  CONFIG SETTING  --------------------------- //
$config['setting']['charset'] = 'utf-8';
$config['setting']['cache'] = 'mysql';
$config['setting']['timezone'] = 'Asia/Shanghai';
$config['setting']['memory_limit'] = '256M';
$config['setting']['filemode'] = 0644;
$config['setting']['authkey'] = '4105d9d7af9fa73f_';
$config['setting']['founder'] = '1';
$config['setting']['development'] = 0;

// --------------------------  CONFIG UPLOAD  --------------------------- //
$config['upload']['image']['extentions'] = array('gif', 'jpg', 'jpeg', 'png');
$config['upload']['image']['limit'] = 5000;
$config['upload']['attachdir'] = 'resource/attachment/';

// --------------------------  CONFIG SITE  --------------------------- //
$config['site']['key'] = '50eca3bfdef9b5534e1f3a092ade9917';
$config['site']['token'] = 'd43f3845737542b63fec4c425a5ad7a3';