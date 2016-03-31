<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
/** @var array $arCurrentValues */

if(!CModule::IncludeModule('iblock'))
	return;

$arTypesEx = CIBlockParameters::GetIBlockTypes(['-' => ' ']);

$arIBlocks = [];
$db_iblock = CIBlock::GetList(
	['SORT' => 'ASC'],
	[
		'SITE_ID' => $_REQUEST['site'],
		'TYPE'    => ($arCurrentValues['IBLOCK_TYPE'] != '-' ? $arCurrentValues['IBLOCK_TYPE'] : '')
	]
);
while ($arRes = $db_iblock->Fetch()) {
	$arIBlocks[$arRes['ID']] = $arRes['NAME'];
}

$arSortFields = [
	'ID'          => GetMessage('SORT_ID'),
	'NAME'        => GetMessage('SORT_NAME'),
	'ACTIVE_FROM' => GetMessage('SORT_ACT'),
	'SORT'        => GetMessage('SORT_SORT'),
	'TIMESTAMP_X' => GetMessage('SORT_TSAMP')
];
$arSorts = [
	'ASC'  => GetMessage('SORT_ASC'),
	'DESC' => GetMessage('SORT_DESC')
];

$arComponentParameters = [
	'GROUPS' => [],
	'PARAMETERS' => [
		'IBLOCK_TYPE' => [
			'PARENT'            => 'BASE',
			'NAME'              => GetMessage('IBLOCK_TYPE'),
			'TYPE'              => 'LIST',
			'VALUES'            => $arTypesEx,
			'DEFAULT'           => '',
			'REFRESH'           => 'Y',
		],
		'IBLOCK_ID' => [
			'PARENT'            => 'BASE',
			'NAME'              => GetMessage('IBLOCK_ID'),
			'TYPE'              => 'LIST',
			'VALUES'            => $arIBlocks,
			'DEFAULT'           => '={$_REQUEST["ID"]}',
			'ADDITIONAL_VALUES' => 'Y',
			'REFRESH'           => 'Y',
		],
		'SORT_BY' => [
			'PARENT'            => 'DATA_SOURCE',
			'NAME'              => GetMessage('SORT_BY'),
			'TYPE'              => 'LIST',
			'DEFAULT'           => 'ACTIVE_FROM',
			'VALUES'            => $arSortFields,
			'ADDITIONAL_VALUES' => 'Y',
		],
		'SORT_ORDER' => [
			'PARENT'            => 'DATA_SOURCE',
			'NAME'              => GetMessage('SORT_ORDER'),
			'TYPE'              => 'LIST',
			'DEFAULT'           => 'DESC',
			'VALUES'            => $arSorts,
			'ADDITIONAL_VALUES' => 'Y',
		],
		'FILTER_NAME' => [
			'PARENT'            => 'DATA_SOURCE',
			'NAME'              => GetMessage('FILTER_NAME'),
			'TYPE'              => 'STRING',
			'DEFAULT'           => 'arFilterMaker',
		],
		'CACHE_TIME' => ['DEFAULT' => 36000000],
		'CACHE_FILTER' => [
			'PARENT'            => 'CACHE_SETTINGS',
			'NAME'              => GetMessage('CACHE_FILTER'),
			'TYPE'              => 'CHECKBOX',
			'DEFAULT'           => 'N',
		],
		'CACHE_GROUPS' => [
			'PARENT'            => 'CACHE_SETTINGS',
			'NAME'              => GetMessage('CACHE_GROUPS'),
			'TYPE'              => 'CHECKBOX',
			'DEFAULT'           => 'Y',
		],
	],
];
