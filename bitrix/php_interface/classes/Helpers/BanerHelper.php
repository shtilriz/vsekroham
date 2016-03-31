<?php
/**
 * Class BanerHelper
 *
 * Класс содержит свойства и методы, помогающие в работе с банерами и слайдерами
 *
 * @author Artem Luchnikov <artem@luchnikov.ru>
 */

class BanerHelper
{
	/**
	 * ID инфоблока с банерами сайта
	 */
	const IBLOCK_ID = 9;

	/**
	 * Массив разделов, в которых не нужно выбводить слайдер в главной версии сайта
	 */
	$arPathHide = array(
		"/buy/",
		"/mebel/",
		"/warranty/",
		"/delivery/",
		"/wholesalers/",
		"/sales/",
		"/our_mags/"
	);

	public function __construct()
	{
		if (!\CModule::IncludeModule('iblock')) {
			throw new Exception('Ошибка создания экземпляра класса. Модуль «Информационные блоки» не найден');
		}
	}

	/**
	 * Возвращает массив банеров по фильтру $arFilter, отсортированные в порядке $arSort
	 *
	 * @param array $arSort
	 * @param array $arFilter
	 *
	 * @return array
	 */
	public static function getList($arSort = array(), $arFilter = array())
	{
		$arBaners = array();
		if (!empty($arFilter)) {
			$rsBaners = CIBlockElement::GetList(
				$arSort,
				$arFilter,
				false,
				false,
				array('IBLOCK_ID', 'ID', 'NAME', 'PREVIEW_PICTURE', 'PROPERTY_LINK', 'PROPERTY_OPEN')
			);
			while ($arBaner = $rsBaners->GetNext()) {
				$arBaners[] = $arBaner;
			}
		}

		return $arBaners;
	}

	/**
	 * Проверяет, нужно ли выводить слайдер банеров в разделе $id
	 *
	 * @param int $sectionId
	 *
	 * @return bool
	 */
	public function isShowSlider($sectionId)
	{
		$isShowSlider = true;
		if ($sectionId) {
			$rsSection = CIBlockSection::GetList(
				array(),
				array(
					'IBLOCK_ID' => IBLOCK_PRODUCT_ID,
					'ACTIVE' => 'Y',
					'ID' => $sectionId
				),
				false,
				array('IBLOCK_ID', 'UF_SLIDER')
			);
			if ($arSection = $rsSection->GetNext()) {
				if ($arSection["UF_SLIDER"])
					$isShowSlider = false;
			}
		}

		return isShowSlider();
	}
}
