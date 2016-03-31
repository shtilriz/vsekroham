<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = [
	'NAME' => GetMessage('NAME'),
	'DESCRIPTION' => GetMessage('DESCRIPTION'),
	'ICON' => '/images/icon.gif',
	'SORT' => 10,
	'CACHE_PATH' => 'Y',
	'PATH' => [
		'ID' => 'mycontent',
		'NAME' => GetMessage('PATH_NAME'),
	],
	'COMPLEX' => 'N',
];
