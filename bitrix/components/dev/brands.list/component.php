<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */

CPageOption::SetOptionString('main', 'nav_page_in_session', 'N');

if (!isset($arParams['CACHE_TIME']))
	$arParams['CACHE_TIME'] = 36000000;

$arParams['IBLOCK_TYPE'] = trim(strip_tags($arParams['IBLOCK_TYPE']));
$arParams['IBLOCK_ID'] = (int)$arParams['IBLOCK_ID'];
if (!$arParams['IBLOCK_ID']) {
	ShowError(GetMessage('IBLOCK_ID_NOT_SETTINGS'));
	return;
}

$arParams['SORT_BY'] = trim(strip_tags($arParams['SORT_BY']));

if (strlen($arParams['SORT_BY']) <= 0) {
	$arParams['SORT_BY'] = 'ACTIVE_FROM';
}

if (!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams['SORT_ORDER'])) {
	$arParams['SORT_ORDER'] = 'DESC';
}

if (strlen($arParams['FILTER_NAME']) <= 0
	|| !preg_match('/^[A-Za-z_][A-Za-z01-9_]*$/', $arParams['FILTER_NAME'])
) {
	$arrFilter = array();
} else {
	$arrFilter = $GLOBALS[$arParams['FILTER_NAME']];
	if (!is_array($arrFilter)) {
		$arrFilter = array();
	}
}

if ($_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest') {
	$this->ClearResultCache([($arParams['CACHE_GROUPS']==='N'? false : $USER->GetGroups()), $arrFilter]);
}

if ($this->StartResultCache(false, [($arParams['CACHE_GROUPS']==='N'? false : $USER->GetGroups()), $arrFilter])) {
	if (!CModule::IncludeModule('iblock')) {
		$this->AbortResultCache();
		ShowError(GetMessage('IBLOCK_MODULE_NOT_INSTALLED'));
		return;
	}

	$arFilter = [
		'IBLOCK_ID' => $arParams['IBLOCK_ID'],
		'ACTIVE'    => 'Y',
	];
	if ($arrFilter['maker']) {
		foreach ($arrFilter['maker'] as $key => $value) {
			$arrFilter['maker'][$key] = "$value%";
		}
		$arFilter['NAME'] = $arrFilter['maker'];
	}

	$rsList = CIBlockElement::GetList(
		[$arParams['SORT_BY'] => $arParams['SORT_ORDER']],
		$arFilter,
		false,
		false,
		array('IBLOCK_ID', 'ID', 'NAME', 'DETAIL_PAGE_URL')
	);
	while ($arList = $rsList->GetNext()) {
		//проверить, если у данного бренда товары
		$arFilterProd = [
			'IBLOCK_ID'      => IBLOCK_PRODUCT_ID,
			'ACTIVE'         => 'Y',
			'PROPERTY_MAKER' => $arList['ID']
		];
		if ($arrFilter['categories']) {
			$arFilterProd['SECTION_ID'] = $arrFilter['categories'];
			$arFilterProd['INCLUDE_SUBSECTIONS'] = 'Y';
		}
		$rsProducts = CIBlockElement::GetList(
			[],
			$arFilterProd,
			false,
			false,
			['ID']
		);
		if ($rsProducts->SelectedRowsCount()) {
			$arResult['MAKERS'][] = $arList;

			$chr = substr($arList['NAME'], 0, 1);
			if (!in_array(strtoupper($chr), $arResult['ABC'])) {
				$arResult['ABC'][] = strtoupper($chr);
			}
		}
	}

	//категории товаров 1 уровня для вывода в фильтре
	$rsSections = CIBlockSection::GetList(
		['SORT' => 'ASC'],
		[
			'IBLOCK_ID'   => IBLOCK_PRODUCT_ID,
			'ACTIVE'      => 'Y',
			'DEPTH_LEVEL' => 1
		],
		false,
		['ID', 'NAME']
	);
	while ($arSection = $rsSections->Fetch()) {
		$arResult['SECTIONS'][$arSection['ID']] = $arSection['NAME'];
	}
}

$this->IncludeComponentTemplate();
