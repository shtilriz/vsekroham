<?php
/**
 * Class IBlockHelper
 *
 * Класс содержит свойства и методы необходимые для работы с модулем 'Информационные блоки'.
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
		$rsEnums = CIBlockPropertyEnum::GetList(
			array('SORT'=>'ASC'),
			array(
				'IBLOCK_ID' => $iblockId,
				'CODE' => $code
			)
		);
		while ($arEnum = $rsEnums->GetNext()) {
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
			$rsElements = CIBlockElement::GetList(
				array(),
				$arFilter,
				false,
				false,
				array("ID")
			);
			$cnt = $rsElements->SelectedRowsCount();
		}
		return $cnt;
	}
}