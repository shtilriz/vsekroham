<?php
/**
 * Скрипт запускается по крону и отправляет СМС клиентам, если товар появился в наличии
 *
 * @link Task: http://p.natix.ru/public/index.php?path_info=projects/2-vsekrohamru/tasks/800
 * @todo Добавить лоирование по отправке СМС
 */

use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;
use Bitrix\Main\Loader;

ignore_user_abort(true);
set_time_limit(0);

if (empty($_SERVER['DOCUMENT_ROOT'])) {
	$_SERVER['HTTP_HOST'] = 'www.vsekroham.ru';
	$_SERVER['DOCUMENT_ROOT'] = realpath(__DIR__ . '/../');
}

define('BX_BUFFER_USED', true);
define('NO_KEEP_STATISTIC', true);
define('NOT_CHECK_PERMISSIONS', true);
define('NO_AGENT_STATISTIC', true);
define('STOP_STATISTICS', true);
define('SITE_ID', 's1');

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

if (!CModule::IncludeModule('highloadblock')) {
	die('Unable to include module "highloadblock"');
}
if (!CModule::IncludeModule('iblock')) {
	die('Unable to include module "iblock"');
}

$arResult = [];
$arProductIDs = [];
$reminderHLBlock = 8;
$hlblock = HL\HighloadBlockTable::getById($reminderHLBlock)->fetch();
$entity = HL\HighloadBlockTable::compileEntity($hlblock);
$entity_data_class = $entity->getDataClass();
$query = new Entity\Query($entity);
$query->setSelect(['*']);
$query->setOrder(['ID' => 'DESC']);
$query->setFilter([
	'>UF_DATE'    => date('d.m.Y', time()-86400*90),
	'UF_DATE_SMS' => false,
]);
$result = $query->exec();
$result = new CDBResult($result);
while ($row = $result->Fetch()) {
	if (!in_array($row['UF_PRODUCT'], $arProductIDs)) {
		$arProductIDs[] = $row['UF_PRODUCT'];
	}

	$arResult[$row['ID']] = [
		'UF_PRODUCT' => $row['UF_PRODUCT'],
		'UF_PHONE'   => $row['UF_PHONE']
	];
}

if (!empty($arProductIDs)) {
	$rsProducts = CIblockElement::GetList(
		[],
		[
			'IBLOCK_ID'           => IBLOCK_PRODUCT_ID,
			'ID'                  => $arProductIDs,
			'!PROPERTY_AVAILABLE' => false
		],
		false,
		false,
		['ID', 'DETAIL_PAGE_URL']
	);
	while ($arProduct = $rsProducts->GetNext()) {
		foreach ($arResult as $rID => $arItem) {
			if ($arItem['UF_PRODUCT'] == $arProduct['ID']) {
				$arEventFields = [
					'PRODUCT_URL' => sprintf('http://%s%s', $_SERVER['HTTP_HOST'], $arProduct['DETAIL_PAGE_URL']),
					'PHONE'       => $arItem['UF_PHONE'],
				];
				CEvent::Send('REMINDER', SITE_ID, $arEventFields);

				//Сохранить дату отправки СМС
				$entity_data_class::update($rID, ['UF_DATE_SMS' => date('d.m.Y')]);
			}
		}
	}
}
