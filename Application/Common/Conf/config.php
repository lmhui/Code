<?php
return array(
	'DB_TYPE'    => 'mysql',
	'DB_HOST'    => 'localhost',
	'DB_NAME'    => 'php34',
	'DB_USER'    => 'root',
	'DB_PWD'     => 'root',
	'DB_PREFIX'  => 'php34_',
	'DB_CHARSET' => 'utf8',
	'DB_PORT'    => '3306',
	/********** 图片相关的配置 ************/
	'IMG_maxSize'  => '3M',
	'IMG_exts'     => array('jpg', 'pjpeg', 'bmp', 'gif', 'png', 'jpeg'),
	'IMG_rootPath' => './Public/Uploads/',
	/********** 修改I函数底层过滤时使用的函数 ***********/
	'DEFAULT_FILTER' => 'trim,removeXSS',
	/********* MD5时用来复杂化的 ****************/
	'MD5_KEY' => 'fdsa#@90#_jl329$9lfds!129',
);