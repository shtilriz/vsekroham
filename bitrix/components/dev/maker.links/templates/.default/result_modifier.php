<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
	die();
}

/**
 * разбить массив на блоки так, чтобы общее кол-во букв, из которых состоит
 * название брендов, в каждом блоке не превышало 75
 */
$cntChars = 0;
$cntResult = count($arResult);
$arBlock = [];
foreach ($arResult as $key => $arBrand) {
	$cntChars += strlen($arBrand['NAME']);
	if ($cntChars <= 75) {
		$arBlock[] = $arBrand;
	} else {
		$arResult['BLOCKS'][] = $arBlock;
		$arBlock = [];
		$cntChars = strlen($arBrand['NAME']);
		$arBlock[] = $arBrand;
	}

	if (($key+1) == $cntResult && !empty($arBlock)) {
		$arResult['BLOCKS'][] = $arBlock;
	}
}
