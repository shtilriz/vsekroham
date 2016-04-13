<?php
/**
 * Скрипт создает запись в higloadblock-таблице, в которой хранятся данные,
 * необходимые для работы уведомлений по смс о поступлении товара
 *
 * @link Task: http://p.natix.ru/public/index.php?path_info=projects/2-vsekrohamru/tasks/800
 */

use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;
use Bitrix\Main\Loader;

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

$arReturn = [
	'STATUS'  => '', //OK - успех, ERROR - ошибка
	'TITLE'   => '',
	'MESSAGE' => '',
];

if (!Loader::includeModule('highloadblock')) {
	$arReturn = [
		'STATUS'  => 'ERROR',
		'TITLE'   => 'Ошибка',
		'MESSAGE' => 'Не подключен модуль "highloadblock".',
	];
	echo json_encode($arReturn);
	die();
}


$productId = (int)$_GET['product_id'];
$phone = trim(strip_tags($_GET['phone']));

if (!$product_id) {
	$arReturn = [
		'STATUS'  => 'ERROR',
		'TITLE'   => 'Ошибка',
		'MESSAGE' => 'Не указан товар, о котором нужно сообщить о поступленни.',
	];
	echo json_encode($arReturn);
	die();
}

if (!$phone) {
	$arReturn = [
		'STATUS'  => 'ERROR',
		'TITLE'   => 'Ошибка',
		'MESSAGE' => 'Не указан номер телефона, на который нужно сообщить о поступленни товара.',
	];
	echo json_encode($arReturn);
	die();
}

$hlblock = HL\HighloadBlockTable::getById(8)->fetch();
$entity = HL\HighloadBlockTable::compileEntity($hlblock);
$entity_data_class = $entity->getDataClass();

$arFields = [
	'UF_PRODUCT' => $product_id,
	'UF_PHONE'   => $phone,
	'UF_DATE'    => date('d.m.Y'),
];

$result = $entity_data_class::add($arFields);

if ($result->isSuccess()) {
	$arReturn = [
		'STATUS'  => 'OK',
		'TITLE'   => 'Сообщение',
		'MESSAGE' => 'Ваша заявка принята. Как только товар появится в наличии, мы отправим уведомление на ваш номер телефона.',
	];
} else {
	$arReturn = [
		'STATUS'  => 'ERROR',
		'TITLE'   => 'Ошибка',
		'MESSAGE' => 'Не указан номер телефона, на который нужно сообщить о поступленни товара.',
	];
}
echo json_encode($arReturn);
