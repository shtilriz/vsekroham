<?php
/**
 * Class CatalogHelper
 *
 * @author Artem Luchnikov <artem@luchnikov.ru>
 */

class CatalogHelper
{
	const IBLOCK_ID = 1;
	const IBLOCK_SKU_ID = 2;

	/**
	 * Возвращает максимальную цену товара в разделе
	 *
	 * @param int $sectionId - ID раздела
	 *
	 * @return mixed
	 */
	public static function getMaxPriceSect($sectionId)
	{
		if (!$sectionId)
			return false;

		CModule::IncludeModule('iblock');
		$rsElements = CIBlockElement::GetList(
			array('CATALOG_PRICE_1' => 'DESC'),
			array(
				'IBLOCK_ID' => self::IBLOCK_ID,
				'ACTIVE' => 'Y',
				'SECTION_ID' => $sectionId,
				'INCLUDE_SUBSECTIONS' => 'Y'
			),
			false,
			array('nTopCount' => 1),
			array('CATALOG_GROUP_1')
		);
		if ($arElement = $rsElements->GetNext()) {
			return $arElement["CATALOG_PRICE_1"];
		}
		else
			return false;
	}

	/**
	 * Возвращает максимальный вес товара в разделе
	 *
	 * @param int $sectionId - ID раздела
	 *
	 * @return mixed
	 */
	public static function getMaxWeightSect($sectionId)
	{
		if (!$sectionId)
			return false;

		CModule::IncludeModule('iblock');
		$rsElements = CIBlockElement::GetList(
			array('CATALOG_WEIGHT_1' => 'DESC'),
			array(
				'IBLOCK_ID' => self::IBLOCK_ID,
				'ACTIVE' => 'Y',
				'SECTION_ID' => $sectionId,
				'INCLUDE_SUBSECTIONS' => 'Y'
			),
			false,
			array('nTopCount' => 1),
			array('CATALOG_GROUP_1')
		);
		if ($arElement = $rsElements->GetNext()) {
			return $arElement["CATALOG_WEIGHT"];
		}
		else
			return false;
	}

	/**
	 * возвращает ID товара по ID торгового предложения или id самого товара, если это не торговое предложение
	 *
	 * @param int $id
	 *
	 * @return int|bool
	 */
	public static function getProductId($id)
	{
		$returnId = $id;

		$arList = CCatalogSku::GetProductInfo($id);
		if (is_array($arList)) {
			$returnId = $arList['ID'];
		}

		return $returnId;
	}
}
