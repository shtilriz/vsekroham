<?php
namespace Vsekroham\Helpers;
/**
 * Class IBlockHelper
 *
 * Класс содержит свойства и методы необходимые для работы с модулем 'Информационные блоки'.
 *
 * @package Vsekroham\Helpers
 *
 * @author Artem Luchnikov <artem@luchnikov.ru>
 */

class IBlockHelper
{
	public function __construct()
	{
		if (!\CModule::IncludeModule('iblock')) {
			throw new Exception('Ошибка создания экземпляра класса. Модуль «Информационные блоки» не найден');
		}
	}

	/**
	 * Метод getPropertyEnumByCode возвращает массив значений свойства типа 'список' по его коду
	 *
	 * @param int $iblockId - ID информационного блока
	 * @param string $code - символьный код свойства
	 *
	 * @return array - возвращает массив [id значения свойства] => [значение свойства]
	 */
	public static function getPropertyEnumByCode($iblockId, $code)
	{
		$arReturn = array();
		$rsEnums = \CIBlockPropertyEnum::GetList(
			['SORT'=>'ASC'],
			[
				'IBLOCK_ID' => $iblockId,
				'CODE' => $code
			]
		);
		while ($arEnum = $rsEnums->Fetch()) {
			$arReturn[$arEnum['ID']] = $arEnum['VALUE'];
		}

		return $arReturn;
	}

	/**
	 * Возвращает количество элементов по фильтру
	 *
	 * @param array $arFilter - массив с полями фильтра
	 *
	 * @return int
	 */
	public static function getCntProductsFiltered($arFilter)
	{
		$cnt = 0;
		if (!empty($arFilter)) {
			$rsElements = \CIBlockElement::GetList(
				[],
				$arFilter,
				false,
				false,
				['ID']
			);
			$cnt = $rsElements->SelectedRowsCount();
		}

		return $cnt;
	}
}
