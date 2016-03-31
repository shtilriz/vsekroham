<?php
/**
 * Class MobBanerHelper
 *
 * Класс содержит свойства и методы, помогающие в работе с банерами в мобильной версии сайта
 *
 * @author Artem Luchnikov <artem@luchnikov.ru>
 */

class MobBanerHelper
{
	/**
	 * ID инфоблока с банерами сайта
	 */
	const IBLOCK_ID = 11;

	/**
	 * ID инфоблока с каталогом товаров
	 */
	const IBLOCK_PRODUCT_ID = 1;

	/**
	 * массив поле инфоблока, которые содержат информацию по банеру
	 */
	protected $arSelect = ['NAME', 'PREVIEW_PICTURE', 'PROPERTY_LINK'];

	private static $instance;
	private function __construct()
	{
		if (!\CModule::IncludeModule('iblock')) {
			throw new Exception('Ошибка создания экземпляра класса. Модуль «Информационные блоки» не найден');
		}
	}
	private function __clone() {}
	private function __wakeup() {}
	public static function getInstance()
	{
		if (empty(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * возвращает массив со всеми доступными для мобильной версии банерами
	 *
	 * @return array
	 */
	public function getList()
	{
		$arBaners = [];

		$rsList = CIBlockElement::GetList(
			['SORT' => 'ASC'],
			[
				'IBLOCK_ID' => self::IBLOCK_ID,
				'ACTIVE'    => 'Y'
			],
			false,
			false,
			$this->arSelect
		);
		while ($arItem = $rsList->Fetch()) {
			$arBaners[] = $arItem;
		}

		return $arBaners;
	}

	/**
	 * возвращает массив с полями случайного банера, который доступен для вывода в заланном разделе сайта
	 * в качестве аргументов могут быть переданы либо id раздела, либо его код
	 *
	 * @param int $sectionId - id разделе каталога товаров
	 * @param int $sectionCode - символьный код раздела каталога
	 *
	 * @return array
	 */
	public function getAvailableBaner($sectionId = 0, $sectionCode = '')
	{
		$sectionId = CIBlockFindTools::GetSectionID(
			$sectionId,
			$sectionCode,
			[
				'IBLOCK_ID' => self::IBLOCK_PRODUCT_ID,
				'ACTIVE'    => 'Y'
			]
		);

		$arBaner = [];
		$arFilter = [
			'IBLOCK_ID' => self::IBLOCK_ID,
			'ACTIVE'    => 'Y'
		];
		if ($sectionId) {
			$arFilter['PROPERTY_SECTIONS' ] = $sectionId;
		}
		$rsList = CIBlockElement::GetList(
			['RAND' => 'ASC'],
			$arFilter,
			false,
			['nTopCount' => 1],
			$this->arSelect
		);
		if ($arItem = $rsList->Fetch()) {
			$arBaner = $arItem;
		}

		return $arBaner;
	}
}
