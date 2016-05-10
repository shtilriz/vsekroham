<?php
/**
 * Скрипт сохраняет заявки из формы 'Нашли дешевле? Сделаем скидку.', а также отправляет уведомление менеджеру
 *
 * @link Task: http://p.natix.ru/public/index.php?path_info=projects%2F2-vsekrohamru%2Ftasks%2F802
 * @author Artem Luchnikov <artem@luchnikov.ru>
 */

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

$arReturn = array();

if (!\CModule::IncludeModule('iblock')) {
	$arReturn = [
		'STATUS'  => 'ERROR',
		'TITLE'   => 'Ошибка',
		'MESSAGE' => 'Возникла внутренняя ошибка на сайте, повторите попытку позже.',
	];
	echo json_encode($arReturn);
	die();
}

//получить входные данные и проверить их значения
$arPostFields = [
	'FIO'        => trim(strip_tags($_POST['FIO'])),
	'PHONE'      => trim(strip_tags($_POST['PHONE'])),
	'EMAIL'      => trim(strip_tags($_POST['EMAIL'])),
	'LINK'       => trim(strip_tags($_POST['LINK'])),
	'PRICE_LINK' => trim(strip_tags($_POST['PRICE_LINK'])),
	'PRODUCT'    => abs((int)$_POST['PRODUCT']),
	'PRICE'      => abs((int)$_POST['PRICE']),
];

$isEmptyField = false;
foreach ($arPostFields as $key => $value) {
	if (!$value) {
		$isEmptyField = true;
		break;
	}
}

if ($isEmptyField) {
	$arReturn = [
		'STATUS'  => 'ERROR',
		'TITLE'   => 'Ошибка',
		'MESSAGE' => 'Не все поля формы заполнены',
	];
	echo json_encode($arReturn);
	die();
}

//если все поля корректно заполены, сохранить данные в ИБ и выслать письмо менеджеру
$el = new CIBlockElement;

$arNewEl = [
	'IBLOCK_ID'         => Vsekroham\Helpers\EnvironmentHelper::getParam('foundCheaperIblockId'),
	'IBLOCK_SECTION_ID' => false,
	'NAME'              => $arPostFields['FIO'],
	'ACTIVE'            => 'N',
	'DATE_ACTIVE_FROM'  => date('d.m.Y H:i'),
	'PROPERTY_VALUES'   => [
		'PHONE'      => $arPostFields['PHONE'],
		'EMAIL'      => $arPostFields['EMAIL'],
		'LINK'       => $arPostFields['LINK'],
		'PRICE_LINK' => $arPostFields['PRICE_LINK'],
		'PRODUCT'    => $arPostFields['PRODUCT'],
		'PRICE'      => $arPostFields['PRICE'],
	],
];

if ($newEl = $el->Add($arNewEl)) {
	$arReturn = [
		'STATUS'  => 'SUCCESS',
		'TITLE'   => 'Ваша завявка принята',
		'MESSAGE' => 'Ваша заявка будет обработана нашим отделом продаж.',
	];

	CEvent::SendImmediate('FOUND_CHEAPER', SITE_ID, $arPostFields);
} else {
	$arReturn = [
		'STATUS'  => 'ERROR',
		'TITLE'   => 'Ошибка',
		'MESSAGE' => 'Не удалось создать заявку. Повторите попытку позже. ' . $el->LAST_ERROR,
	];
}

echo json_encode($arReturn);
